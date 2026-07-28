<?php

namespace App\Support;

use App\Models\Badge;
use App\Models\Visit;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class BadgeLifecycle
{
    /**
     * Atomically check a visit in and claim exactly one badge.
     *
     * @return array{result: string, visit: Visit, badge: ?Badge, requested_badge_no: ?string}
     */
    public function checkIn(Visit $visit, CarbonInterface $checkedInAt): array
    {
        return DB::transaction(function () use ($visit, $checkedInAt): array {
            $lockedVisit = Visit::query()
                ->with('visitor')
                ->lockForUpdate()
                ->findOrFail($visit->id);

            $existingBadge = Badge::query()
                ->where('visit_id', $lockedVisit->id)
                ->where('status', 'active')
                ->lockForUpdate()
                ->first();

            if ($lockedVisit->status === 'checked_in') {
                return [
                    'result' => 'already_checked_in',
                    'visit' => $lockedVisit,
                    'badge' => $existingBadge,
                    'requested_badge_no' => $this->requestedBadgeNo($lockedVisit),
                ];
            }

            if ($lockedVisit->status !== 'approved') {
                return [
                    'result' => 'invalid_status',
                    'visit' => $lockedVisit,
                    'badge' => $existingBadge,
                    'requested_badge_no' => $this->requestedBadgeNo($lockedVisit),
                ];
            }

            $badge = $existingBadge;
            $requestedBadgeNo = $this->requestedBadgeNo($lockedVisit);

            if ($badge === null) {
                $badgeQuery = Badge::query()
                    ->where('status', 'available')
                    ->lockForUpdate();

                if ($lockedVisit->requested_badge_id !== null) {
                    $badgeQuery->whereKey($lockedVisit->requested_badge_id);
                } elseif ($requestedBadgeNo !== null) {
                    $badgeQuery->where('badge_no', $requestedBadgeNo);
                } else {
                    $badgeQuery->orderBy('id');
                }

                $badge = $badgeQuery->first();

                if ($badge === null) {
                    if ($requestedBadgeNo === null) {
                        $lockedVisit->update([
                            'status' => 'checked_in',
                            'actual_checkin_at' => $checkedInAt,
                        ]);

                        return [
                            'result' => 'checked_in',
                            'visit' => $lockedVisit->refresh(),
                            'badge' => null,
                            'requested_badge_no' => null,
                        ];
                    }

                    return [
                        'result' => 'requested_badge_unavailable',
                        'visit' => $lockedVisit,
                        'badge' => null,
                        'requested_badge_no' => $requestedBadgeNo,
                    ];
                }

                $badge->update([
                    'visit_id' => $lockedVisit->id,
                    'status' => 'active',
                    'issued_at' => now(),
                    'revoked_at' => null,
                    'valid_until' => $lockedVisit->expected_checkout_at,
                ]);
            }

            $lockedVisit->update([
                'status' => 'checked_in',
                'actual_checkin_at' => $checkedInAt,
            ]);

            return [
                'result' => 'checked_in',
                'visit' => $lockedVisit->refresh(),
                'badge' => $badge->refresh(),
                'requested_badge_no' => $requestedBadgeNo,
            ];
        }, 3);
    }

    /**
     * Atomically check a visit out and return every accidentally duplicated badge
     * to the reusable pool.
     *
     * @return array{result: string, visit: Visit, badge: ?Badge}
     */
    public function checkOut(Visit $visit, CarbonInterface $checkedOutAt): array
    {
        return DB::transaction(function () use ($visit, $checkedOutAt): array {
            $lockedVisit = Visit::query()
                ->lockForUpdate()
                ->findOrFail($visit->id);

            if ($lockedVisit->status !== 'checked_in') {
                return [
                    'result' => 'invalid_status',
                    'visit' => $lockedVisit,
                    'badge' => null,
                ];
            }

            $activeBadges = Badge::query()
                ->where('visit_id', $lockedVisit->id)
                ->where('status', 'active')
                ->orderByRaw('CASE WHEN id = ? THEN 0 ELSE 1 END', [$lockedVisit->requested_badge_id ?? 0])
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $primaryBadge = $activeBadges->first();

            if ($activeBadges->isNotEmpty()) {
                Badge::query()
                    ->whereKey($activeBadges->modelKeys())
                    ->update([
                        'visit_id' => null,
                        'status' => 'available',
                        'issued_at' => null,
                        'revoked_at' => null,
                        'valid_until' => null,
                        'updated_at' => now(),
                    ]);
            }

            $lockedVisit->update([
                'status' => 'checked_out',
                'actual_checkout_at' => $checkedOutAt,
            ]);

            return [
                'result' => 'checked_out',
                'visit' => $lockedVisit->refresh(),
                'badge' => $primaryBadge,
            ];
        }, 3);
    }

    private function requestedBadgeNo(Visit $visit): ?string
    {
        if ($visit->requested_badge_id !== null) {
            $badgeNo = Badge::query()
                ->whereKey($visit->requested_badge_id)
                ->value('badge_no');

            if (is_string($badgeNo) && trim($badgeNo) !== '') {
                return trim($badgeNo);
            }
        }

        $legacyBadgeNo = trim((string) ($visit->visitor?->visitor_id_card_number ?? ''));

        return $legacyBadgeNo !== '' ? $legacyBadgeNo : null;
    }
}
