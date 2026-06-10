<?php

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Run this tool from command line.\n");
    exit(1);
}

$options = getopt('', [
    'device:',
    'customer:',
    'expires::',
    'months::',
    'edition::',
    'features::',
    'private-key::',
    'out::',
    'help',
]);

if (isset($options['help']) || empty($options['device']) || empty($options['customer'])) {
    fwrite(STDOUT, <<<TEXT
Usage:
  php license-issuer/issue.php --device=VMS-XXXXXX-XXXXXX-XXXXXX-XXXXXX --customer="Ten khach" --expires=2027-06-08 --out=license.json

Options:
  --device       Ma may chu tren man hinh Kich hoat ban quyen
  --customer     Ten khach hang
  --expires      Ngay het han YYYY-MM-DD
  --months       So thang hieu luc tinh tu hom nay, vi du 3, 6, 9, 12
  --edition      Goi ban quyen, mac dinh standard
  --features     Danh sach tinh nang, ngan cach bang dau phay
  --private-key  Duong dan private key, mac dinh license-issuer/private/license-private.pem
  --out          File xuat license JSON

TEXT);
    exit(empty($options['help']) ? 1 : 0);
}

$deviceId = strtoupper(trim((string) $options['device']));
if (! preg_match('/^VMS-[A-Z0-9]{6}-[A-Z0-9]{6}-[A-Z0-9]{6}-[A-Z0-9]{6}$/', $deviceId)) {
    fwrite(STDERR, "Ma may chu khong dung dinh dang.\n");
    exit(1);
}

$privateKeyPath = (string) ($options['private-key'] ?? __DIR__.'/private/license-private.pem');
if (! is_file($privateKeyPath)) {
    fwrite(STDERR, "Khong tim thay private key: {$privateKeyPath}\n");
    exit(1);
}

$expiresAt = trim((string) ($options['expires'] ?? ''));
$months = trim((string) ($options['months'] ?? ''));
if ($expiresAt !== '' && ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $expiresAt)) {
    fwrite(STDERR, "Ngay het han phai co dinh dang YYYY-MM-DD.\n");
    exit(1);
}
if ($months !== '') {
    if (! ctype_digit($months) || (int) $months < 1 || (int) $months > 120) {
        fwrite(STDERR, "So thang phai tu 1 den 120.\n");
        exit(1);
    }

    $expiresAt = (new DateTimeImmutable('today'))->modify('+'.((int) $months).' months')->format('Y-m-d');
}

$features = array_values(array_filter(array_map(
    fn (string $feature): string => trim($feature),
    explode(',', (string) ($options['features'] ?? 'core,kiosk,email,reports')),
)));

$payload = [
    'product' => 'khach-moi-vms',
    'license_id' => 'LIC-'.strtoupper(bin2hex(random_bytes(8))),
    'customer' => trim((string) $options['customer']),
    'device_id' => $deviceId,
    'edition' => trim((string) ($options['edition'] ?? 'standard')),
    'features' => $features,
    'issued_at' => gmdate('Y-m-d\TH:i:s\Z'),
    'expires_at' => $expiresAt !== '' ? $expiresAt : null,
];

$privateKey = file_get_contents($privateKeyPath);
$signature = '';
$signed = openssl_sign(canonicalJson($payload), $signature, $privateKey, OPENSSL_ALGO_SHA256);
if (! $signed) {
    fwrite(STDERR, "Khong ky duoc license. Kiem tra private key va OpenSSL.\n");
    exit(1);
}

$document = [
    'payload' => $payload,
    'signature' => base64UrlEncode($signature),
];

$output = json_encode($document, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).PHP_EOL;
$outPath = (string) ($options['out'] ?? '');
if ($outPath !== '') {
    file_put_contents($outPath, $output);
    fwrite(STDOUT, "Da tao license: {$outPath}\n");
} else {
    fwrite(STDOUT, $output);
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
