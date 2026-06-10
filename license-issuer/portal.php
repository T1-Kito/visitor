<?php

declare(strict_types=1);

session_start();

if (! in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit('Trang quản lý license chỉ được phép chạy trên máy nội bộ.');
}

$root = __DIR__;
$dataDirectory = $root.'/data';
$databasePath = $dataDirectory.'/licenses.json';
$privateKeyPath = $root.'/private/license-private.pem';

if (! is_dir($dataDirectory)) {
    mkdir($dataDirectory, 0755, true);
}

if (! is_file($databasePath)) {
    file_put_contents($databasePath, json_encode(['licenses' => []], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

if (empty($_SESSION['license_csrf'])) {
    $_SESSION['license_csrf'] = bin2hex(random_bytes(24));
}

$action = (string) ($_GET['action'] ?? '');

if ($action === 'download') {
    $record = findLicense(loadDatabase($databasePath), (string) ($_GET['id'] ?? ''));
    if ($record === null) {
        http_response_code(404);
        exit('Không tìm thấy license.');
    }

    $fileName = 'license-'.slugify((string) $record['customer']).'.json';
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="'.$fileName.'"');
    header('Cache-Control: no-store');
    echo json_encode($record['document'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (! hash_equals((string) $_SESSION['license_csrf'], (string) ($_POST['_token'] ?? ''))) {
        flash('error', 'Phiên làm việc không hợp lệ. Vui lòng thử lại.');
        redirectHome();
    }

    $postAction = (string) ($_POST['action'] ?? '');

    try {
        if ($postAction === 'issue') {
            issueLicense($databasePath, $privateKeyPath);
            flash('success', 'Đã cấp license và lưu vào danh sách.');
        } elseif ($postAction === 'revoke') {
            updateLicenseStatus($databasePath, (string) ($_POST['id'] ?? ''), 'revoked');
            flash('success', 'Đã đánh dấu thu hồi license trong sổ quản lý.');
        } elseif ($postAction === 'restore') {
            updateLicenseStatus($databasePath, (string) ($_POST['id'] ?? ''), 'active');
            flash('success', 'Đã khôi phục trạng thái license trong sổ quản lý.');
        }
    } catch (Throwable $exception) {
        flash('error', $exception->getMessage());
    }

    redirectHome();
}

$database = loadDatabase($databasePath);
$query = mb_strtolower(trim((string) ($_GET['q'] ?? '')));
$records = array_reverse($database['licenses']);

if ($query !== '') {
    $records = array_values(array_filter($records, static function (array $record) use ($query): bool {
        $haystack = mb_strtolower(implode(' ', [
            (string) ($record['customer'] ?? ''),
            (string) ($record['device_id'] ?? ''),
            (string) ($record['license_id'] ?? ''),
        ]));

        return str_contains($haystack, $query);
    }));
}

$today = new DateTimeImmutable('today');
$activeCount = 0;
$revokedCount = 0;
$expiringCount = 0;

foreach ($database['licenses'] as $record) {
    if (($record['status'] ?? 'active') === 'revoked') {
        $revokedCount++;
        continue;
    }

    $activeCount++;
    if (! empty($record['expires_at'])) {
        $expiry = new DateTimeImmutable((string) $record['expires_at']);
        $days = (int) $today->diff($expiry)->format('%r%a');
        if ($days >= 0 && $days <= 30) {
            $expiringCount++;
        }
    }
}

$flash = $_SESSION['license_flash'] ?? null;
unset($_SESSION['license_flash']);

function issueLicense(string $databasePath, string $privateKeyPath): void
{
    $deviceId = strtoupper(trim((string) ($_POST['device_id'] ?? '')));
    $customer = trim((string) ($_POST['customer'] ?? ''));
    $duration = trim((string) ($_POST['duration'] ?? '12'));
    $expiresAt = trim((string) ($_POST['expires_at'] ?? ''));
    $edition = trim((string) ($_POST['edition'] ?? 'standard'));
    $notes = trim((string) ($_POST['notes'] ?? ''));

    if (preg_match('/^VMS-[A-Z0-9]{6}-[A-Z0-9]{6}-[A-Z0-9]{6}-[A-Z0-9]{6}$/', $deviceId) !== 1) {
        throw new RuntimeException('Mã máy chủ không đúng định dạng.');
    }

    if ($customer === '') {
        throw new RuntimeException('Vui lòng nhập tên khách hàng.');
    }

    if (in_array($duration, ['3', '6', '9', '12'], true)) {
        $expiresAt = (new DateTimeImmutable('today'))->modify("+{$duration} months")->format('Y-m-d');
    } elseif ($duration === 'permanent') {
        $expiresAt = '';
    } elseif ($duration !== 'custom') {
        throw new RuntimeException('Thời hạn license không hợp lệ.');
    }

    if ($duration === 'custom' && $expiresAt === '') {
        throw new RuntimeException('Vui lòng nhập ngày hết hạn tùy chọn.');
    }

    if ($expiresAt !== '') {
        $expiry = DateTimeImmutable::createFromFormat('!Y-m-d', $expiresAt);
        if (! $expiry || $expiry->format('Y-m-d') !== $expiresAt) {
            throw new RuntimeException('Ngày hết hạn không hợp lệ.');
        }
    }

    if (! is_file($privateKeyPath)) {
        throw new RuntimeException('Không tìm thấy private key cấp license.');
    }

    $payload = [
        'product' => 'khach-moi-vms',
        'license_id' => 'LIC-'.strtoupper(bin2hex(random_bytes(8))),
        'customer' => $customer,
        'device_id' => $deviceId,
        'edition' => $edition !== '' ? $edition : 'standard',
        'features' => ['core', 'kiosk', 'email', 'reports'],
        'issued_at' => gmdate('Y-m-d\TH:i:s\Z'),
        'expires_at' => $expiresAt !== '' ? $expiresAt : null,
    ];

    $signature = '';
    $signed = openssl_sign(
        canonicalJson($payload),
        $signature,
        (string) file_get_contents($privateKeyPath),
        OPENSSL_ALGO_SHA256,
    );

    if (! $signed) {
        throw new RuntimeException('Không ký được license. Vui lòng kiểm tra OpenSSL và private key.');
    }

    $document = [
        'payload' => $payload,
        'signature' => base64UrlEncode($signature),
    ];

    mutateDatabase($databasePath, static function (array $database) use ($payload, $document, $notes): array {
        $database['licenses'][] = [
            'id' => bin2hex(random_bytes(10)),
            'license_id' => $payload['license_id'],
            'customer' => $payload['customer'],
            'device_id' => $payload['device_id'],
            'edition' => $payload['edition'],
            'issued_at' => $payload['issued_at'],
            'expires_at' => $payload['expires_at'],
            'status' => 'active',
            'notes' => $notes,
            'revoked_at' => null,
            'document' => $document,
        ];

        return $database;
    });
}

function updateLicenseStatus(string $databasePath, string $id, string $status): void
{
    mutateDatabase($databasePath, static function (array $database) use ($id, $status): array {
        $found = false;
        foreach ($database['licenses'] as &$record) {
            if (($record['id'] ?? '') !== $id) {
                continue;
            }

            $record['status'] = $status;
            $record['revoked_at'] = $status === 'revoked' ? gmdate('Y-m-d\TH:i:s\Z') : null;
            $found = true;
            break;
        }
        unset($record);

        if (! $found) {
            throw new RuntimeException('Không tìm thấy license cần cập nhật.');
        }

        return $database;
    });
}

/**
 * @return array{licenses: array<int, array<string, mixed>>}
 */
function loadDatabase(string $path): array
{
    $decoded = json_decode((string) file_get_contents($path), true);

    return is_array($decoded) && isset($decoded['licenses']) && is_array($decoded['licenses'])
        ? $decoded
        : ['licenses' => []];
}

/**
 * @param  callable(array{licenses: array<int, array<string, mixed>>}): array{licenses: array<int, array<string, mixed>>}  $callback
 */
function mutateDatabase(string $path, callable $callback): void
{
    $handle = fopen($path, 'c+');
    if ($handle === false) {
        throw new RuntimeException('Không mở được dữ liệu quản lý license.');
    }

    try {
        if (! flock($handle, LOCK_EX)) {
            throw new RuntimeException('Không khóa được dữ liệu quản lý license.');
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        $database = json_decode($content ?: '', true);
        if (! is_array($database) || ! isset($database['licenses'])) {
            $database = ['licenses' => []];
        }

        $database = $callback($database);
        ftruncate($handle, 0);
        rewind($handle);
        fwrite($handle, json_encode($database, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        fflush($handle);
        flock($handle, LOCK_UN);
    } finally {
        fclose($handle);
    }
}

/**
 * @param  array{licenses: array<int, array<string, mixed>>}  $database
 * @return array<string, mixed>|null
 */
function findLicense(array $database, string $id): ?array
{
    foreach ($database['licenses'] as $record) {
        if (($record['id'] ?? '') === $id) {
            return $record;
        }
    }

    return null;
}

/**
 * @param  array<string, mixed>  $payload
 */
function canonicalJson(array $payload): string
{
    return json_encode(sortKeys($payload), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

/**
 * @param  mixed  $value
 * @return mixed
 */
function sortKeys($value)
{
    if (! is_array($value)) {
        return $value;
    }

    if (! array_is_list($value)) {
        ksort($value);
    }

    foreach ($value as $key => $item) {
        $value[$key] = sortKeys($item);
    }

    return $value;
}

function base64UrlEncode(string $value): string
{
    return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
}

function slugify(string $value): string
{
    $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: 'khach-hang';
    $value = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $value) ?? 'khach-hang');

    return trim($value, '-') ?: 'khach-hang';
}

function flash(string $type, string $message): void
{
    $_SESSION['license_flash'] = ['type' => $type, 'message' => $message];
}

function redirectHome(): never
{
    header('Location: /');
    exit;
}

function expiryLabel(array $record, DateTimeImmutable $today): array
{
    if (empty($record['expires_at'])) {
        return ['Vĩnh viễn', 'permanent'];
    }

    $expiry = new DateTimeImmutable((string) $record['expires_at']);
    $days = (int) $today->diff($expiry)->format('%r%a');

    if ($days < 0) {
        return ['Đã hết hạn', 'expired'];
    }

    if ($days === 0) {
        return ['Hết hạn hôm nay', 'warning'];
    }

    return ["Còn {$days} ngày", $days <= 30 ? 'warning' : 'active'];
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý bản quyền | Khách Mời VMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root{--blue:#126ed5;--blue-soft:#edf5ff;--text:#10233d;--muted:#71849b;--line:#dfe8f2;--page:#f3f7fb;--green:#11855f;--red:#d33f4f;--amber:#b96a08}*{box-sizing:border-box}body{margin:0;background:var(--page);color:var(--text);font-family:Manrope,"Segoe UI",sans-serif}.shell{width:min(1420px,calc(100% - 40px));margin:0 auto;padding:28px 0 40px}.topbar{display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:22px}.topbar h1{margin:0;font-size:1.45rem}.topbar p{margin:5px 0 0;color:var(--muted);font-size:.82rem}.local-badge{display:inline-flex;align-items:center;gap:7px;padding:9px 12px;border-radius:8px;background:#eaf8f2;color:var(--green);font-size:.75rem;font-weight:700}.stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:18px}.stat{padding:18px;border:1px solid var(--line);border-radius:9px;background:#fff}.stat span{color:var(--muted);font-size:.75rem}.stat strong{display:block;margin-top:5px;font-size:1.55rem}.workspace{display:grid;grid-template-columns:360px minmax(0,1fr);gap:18px;align-items:start}.panel{border:1px solid var(--line);border-radius:10px;background:#fff;box-shadow:0 10px 26px rgba(17,39,68,.035)}.panel-head{padding:18px 20px;border-bottom:1px solid #edf2f7}.panel-head h2{margin:0;font-size:1rem}.panel-head p{margin:5px 0 0;color:var(--muted);font-size:.73rem}.form{display:grid;gap:13px;padding:18px 20px}.field{display:grid;gap:6px}.field label{font-size:.72rem;font-weight:600;color:#405772}.field input,.field select,.field textarea,.search{width:100%;min-height:40px;padding:9px 11px;border:1px solid #d8e4f0;border-radius:7px;background:#fbfdff;color:#203a57;outline:0;font:inherit;font-size:.78rem}.field textarea{min-height:75px;resize:vertical}.field input:focus,.field textarea:focus,.search:focus{border-color:#78ace8;box-shadow:0 0 0 3px rgba(18,110,213,.08)}.btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:38px;padding:0 13px;border:1px solid transparent;border-radius:7px;background:#fff;color:#334d69;font:inherit;font-size:.74rem;font-weight:600;text-decoration:none;cursor:pointer}.btn-primary{background:var(--blue);color:#fff}.btn-outline{border-color:#d8e4f0}.btn-danger{border-color:#f0c4c9;color:var(--red)}.btn-success{border-color:#bce3d4;color:var(--green)}.toolbar{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 16px;border-bottom:1px solid #edf2f7}.search-form{width:min(430px,100%)}.table-wrap{overflow:auto}.table{width:100%;border-collapse:collapse;white-space:nowrap}.table th,.table td{padding:13px 15px;border-bottom:1px solid #edf2f7;text-align:left;font-size:.74rem}.table th{color:#71849b;background:#f8fbfe;font-size:.68rem;text-transform:uppercase}.customer{font-weight:700}.sub{display:block;margin-top:3px;color:#8193a8;font-size:.68rem}.status{display:inline-flex;padding:5px 8px;border-radius:999px;font-size:.67rem;font-weight:700}.status.active,.status.permanent{background:#eaf8f2;color:var(--green)}.status.warning{background:#fff5df;color:var(--amber)}.status.expired,.status.revoked{background:#fff0f2;color:var(--red)}.actions{display:flex;gap:6px}.empty{padding:45px;text-align:center;color:var(--muted);font-size:.82rem}.notice{margin-bottom:16px;padding:12px 14px;border:1px solid;border-radius:8px;font-size:.78rem}.notice.success{border-color:#bce3d4;background:#effaf5;color:var(--green)}.notice.error{border-color:#f0c4c9;background:#fff3f4;color:var(--red)}.offline-note{margin:0 20px 18px;padding:11px 12px;border:1px solid #f0d7a8;border-radius:8px;background:#fff9ed;color:#8b5a0a;font-size:.7rem;line-height:1.5}@media(max-width:980px){.workspace{grid-template-columns:1fr}.stats{grid-template-columns:repeat(2,1fr)}}@media(max-width:600px){.shell{width:min(100% - 20px,1420px);padding-top:16px}.topbar{align-items:flex-start;flex-direction:column}.stats{grid-template-columns:1fr 1fr}.toolbar{align-items:stretch;flex-direction:column}.search-form{width:100%}}
        .duration-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}.duration-note{color:#8193a8;font-size:.68rem}.field select:focus{border-color:#78ace8;box-shadow:0 0 0 3px rgba(18,110,213,.08)}@media(max-width:600px){.duration-grid{grid-template-columns:1fr}}
    </style>
</head>
<body>
<main class="shell">
    <header class="topbar">
        <div>
            <h1>Quản lý bản quyền</h1>
            <p>Danh sách license nội bộ của Khách Mời VMS</p>
        </div>
        <span class="local-badge"><i class="bi bi-shield-lock-fill"></i>Chỉ chạy trên máy này</span>
    </header>

    <?php if ($flash): ?>
        <div class="notice <?= htmlspecialchars((string) $flash['type']) ?>"><?= htmlspecialchars((string) $flash['message']) ?></div>
    <?php endif; ?>

    <section class="stats">
        <div class="stat"><span>Tổng license</span><strong><?= count($database['licenses']) ?></strong></div>
        <div class="stat"><span>Đang theo dõi</span><strong><?= $activeCount ?></strong></div>
        <div class="stat"><span>Sắp hết hạn 30 ngày</span><strong><?= $expiringCount ?></strong></div>
        <div class="stat"><span>Đã thu hồi nội bộ</span><strong><?= $revokedCount ?></strong></div>
    </section>

    <div class="workspace">
        <section class="panel">
            <div class="panel-head">
                <h2>Cấp license mới</h2>
                <p>Nhập mã máy chủ do khách gửi từ trang kích hoạt.</p>
            </div>
            <form class="form" method="post">
                <input type="hidden" name="_token" value="<?= htmlspecialchars((string) $_SESSION['license_csrf']) ?>">
                <input type="hidden" name="action" value="issue">
                <div class="field">
                    <label>Mã máy chủ</label>
                    <input name="device_id" required placeholder="VMS-XXXXXX-XXXXXX-XXXXXX-XXXXXX">
                </div>
                <div class="field">
                    <label>Khách hàng / Công ty</label>
                    <input name="customer" required placeholder="Công ty ABC">
                </div>
                <div class="field">
                    <label>Thời hạn license</label>
                    <div class="duration-grid">
                        <select name="duration" data-duration-select>
                            <option value="3">3 tháng</option>
                            <option value="6">6 tháng</option>
                            <option value="9">9 tháng</option>
                            <option value="12" selected>12 tháng</option>
                            <option value="custom">Tùy chọn ngày hết hạn</option>
                            <option value="permanent">Vĩnh viễn</option>
                        </select>
                        <input name="expires_at" type="date" data-custom-expiry disabled>
                    </div>
                    <span class="duration-note">Mặc định cấp 12 tháng để tránh cấp vĩnh viễn nhầm.</span>
                </div>
                <div class="field">
                    <label>Gói bản quyền</label>
                    <select name="edition">
                        <option value="standard">Tiêu chuẩn</option>
                        <option value="professional">Chuyên nghiệp</option>
                        <option value="enterprise">Doanh nghiệp</option>
                    </select>
                </div>
                <div class="field">
                    <label>Ghi chú nội bộ</label>
                    <textarea name="notes" placeholder="Hợp đồng, người liên hệ..."></textarea>
                </div>
                <button class="btn btn-primary" type="submit"><i class="bi bi-key"></i>Cấp và lưu license</button>
            </form>
        </section>

        <section class="panel">
            <div class="panel-head">
                <h2>Danh sách license</h2>
                <p>Theo dõi khách hàng, mã máy, thời hạn và lịch sử thu hồi.</p>
            </div>
            <div class="toolbar">
                <form class="search-form" method="get">
                    <input class="search" name="q" value="<?= htmlspecialchars((string) ($_GET['q'] ?? '')) ?>" placeholder="Tìm tên khách, mã máy hoặc mã license...">
                </form>
                <?php if ($query !== ''): ?><a class="btn btn-outline" href="/">Xóa lọc</a><?php endif; ?>
            </div>
            <p class="offline-note"><strong>Lưu ý:</strong> Thu hồi tại đây chỉ cập nhật sổ quản lý nội bộ. License offline đã gửi cho khách vẫn chạy đến ngày hết hạn nếu máy khách không kết nối về máy chủ license.</p>
            <div class="table-wrap">
                <?php if ($records === []): ?>
                    <div class="empty">Chưa có license phù hợp.</div>
                <?php else: ?>
                    <table class="table">
                        <thead><tr><th>Khách hàng</th><th>Mã máy chủ</th><th>Ngày cấp</th><th>Thời hạn</th><th>Trạng thái</th><th></th></tr></thead>
                        <tbody>
                        <?php foreach ($records as $record): ?>
                            <?php [$expiryText, $expiryClass] = expiryLabel($record, $today); ?>
                            <?php $revoked = ($record['status'] ?? 'active') === 'revoked'; ?>
                            <tr>
                                <td><span class="customer"><?= htmlspecialchars((string) $record['customer']) ?></span><span class="sub"><?= htmlspecialchars((string) $record['license_id']) ?></span></td>
                                <td><?= htmlspecialchars((string) $record['device_id']) ?></td>
                                <td><?= htmlspecialchars(substr((string) $record['issued_at'], 0, 10)) ?></td>
                                <td><span class="status <?= $revoked ? 'revoked' : $expiryClass ?>"><?= $revoked ? 'Đã thu hồi' : htmlspecialchars($expiryText) ?></span></td>
                                <td><?= htmlspecialchars(ucfirst((string) $record['edition'])) ?></td>
                                <td>
                                    <div class="actions">
                                        <a class="btn btn-outline" href="/?action=download&id=<?= urlencode((string) $record['id']) ?>" title="Tải file license"><i class="bi bi-download"></i></a>
                                        <form method="post">
                                            <input type="hidden" name="_token" value="<?= htmlspecialchars((string) $_SESSION['license_csrf']) ?>">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars((string) $record['id']) ?>">
                                            <input type="hidden" name="action" value="<?= $revoked ? 'restore' : 'revoke' ?>">
                                            <button class="btn <?= $revoked ? 'btn-success' : 'btn-danger' ?>" type="submit" title="<?= $revoked ? 'Khôi phục' : 'Thu hồi nội bộ' ?>" onclick="return confirm('Xác nhận thay đổi trạng thái license này?')"><i class="bi <?= $revoked ? 'bi-arrow-counterclockwise' : 'bi-slash-circle' ?>"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </section>
    </div>
</main>
<script>
    const durationSelect = document.querySelector('[data-duration-select]');
    const customExpiry = document.querySelector('[data-custom-expiry]');
    const syncCustomExpiry = () => {
        const custom = durationSelect?.value === 'custom';
        if (!customExpiry) return;
        customExpiry.disabled = !custom;
        customExpiry.required = custom;
        if (!custom) customExpiry.value = '';
    };
    durationSelect?.addEventListener('change', syncCustomExpiry);
    syncCustomExpiry();
</script>
</body>
</html>
