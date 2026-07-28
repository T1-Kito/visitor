<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('visits', 'requested_badge_id')) {
            Schema::table('visits', function (Blueprint $table): void {
                $table->foreignId('requested_badge_id')
                    ->nullable()
                    ->after('checkin_method')
                    ->constrained('badges')
                    ->nullOnDelete();
            });
        }

        DB::table('visits')
            ->join('visitors', 'visitors.id', '=', 'visits.visitor_id')
            ->join('badges', function ($join): void {
                $join
                    ->on('badges.badge_no', '=', 'visitors.visitor_id_card_number')
                    ->on('badges.tenant_id', '=', 'visits.tenant_id');
            })
            ->whereNull('visits.requested_badge_id')
            ->whereIn('visits.status', ['pending', 'approved', 'checked_in'])
            ->select(['visits.id as visit_id', 'badges.id as badge_id'])
            ->orderBy('visits.id')
            ->get()
            ->each(function (object $row): void {
                DB::table('visits')
                    ->where('id', $row->visit_id)
                    ->update(['requested_badge_id' => $row->badge_id]);
            });

        // Old checkout code marked reusable cards as revoked and kept the visit link.
        DB::table('badges')
            ->whereNotNull('visit_id')
            ->where('status', '!=', 'active')
            ->update([
                'visit_id' => null,
                'status' => 'available',
                'issued_at' => null,
                'revoked_at' => null,
                'valid_until' => null,
                'updated_at' => now(),
            ]);

        DB::table('badges')
            ->where('status', 'active')
            ->where(function ($query): void {
                $query
                    ->whereNull('visit_id')
                    ->orWhereNotExists(function ($visits): void {
                        $visits
                            ->selectRaw('1')
                            ->from('visits')
                            ->whereColumn('visits.id', 'badges.visit_id')
                            ->where('visits.status', 'checked_in');
                    });
            })
            ->update([
                'visit_id' => null,
                'status' => 'available',
                'issued_at' => null,
                'revoked_at' => null,
                'valid_until' => null,
                'updated_at' => now(),
            ]);

        DB::table('visits')
            ->where('status', 'checked_in')
            ->orderBy('id')
            ->eachById(function (object $visit): void {
                $activeBadges = DB::table('badges')
                    ->where('visit_id', $visit->id)
                    ->where('status', 'active')
                    ->orderBy('id')
                    ->get(['id']);

                if ($activeBadges->count() <= 1) {
                    return;
                }

                $activeBadgeIds = $activeBadges->pluck('id')->map(fn ($id): int => (int) $id);
                $requestedBadgeId = (int) ($visit->requested_badge_id ?? 0);
                $firstCheckedInBadgeId = DB::table('access_control_logs')
                    ->where('visit_id', $visit->id)
                    ->where('event', 'CHECK_IN')
                    ->whereIn('badge_id', $activeBadgeIds)
                    ->oldest('id')
                    ->value('badge_id');
                $keepBadgeId = (int) ($firstCheckedInBadgeId
                    ?? ($activeBadgeIds->contains($requestedBadgeId) ? $requestedBadgeId : null)
                    ?? $activeBadgeIds->first());

                DB::table('visits')
                    ->where('id', $visit->id)
                    ->update(['requested_badge_id' => $keepBadgeId]);

                DB::table('badges')
                    ->whereIn('id', $activeBadgeIds->reject(fn (int $id): bool => $id === $keepBadgeId))
                    ->update([
                        'visit_id' => null,
                        'status' => 'available',
                        'issued_at' => null,
                        'revoked_at' => null,
                        'valid_until' => null,
                        'updated_at' => now(),
                    ]);
            }, 'id');

        if (! Schema::hasIndex('badges', 'badges_visit_id_unique', 'unique')) {
            Schema::table('badges', function (Blueprint $table): void {
                $table->unique('visit_id', 'badges_visit_id_unique');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('badges', 'badges_visit_id_unique', 'unique')) {
            Schema::table('badges', function (Blueprint $table): void {
                $table->dropUnique('badges_visit_id_unique');
            });
        }

        if (Schema::hasColumn('visits', 'requested_badge_id')) {
            Schema::table('visits', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('requested_badge_id');
            });
        }
    }
};
