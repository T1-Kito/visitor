<?php

return [
    'enforced' => (bool) env('LICENSE_ENFORCED', env('APP_ENV') === 'production'),
    'device_id_path' => env('LICENSE_DEVICE_ID_PATH') ?: storage_path('app/license/device-id.txt'),
    'storage_path' => env('LICENSE_STORAGE_PATH') ?: storage_path('app/license/license.json'),
    'trial_started_at_path' => env('LICENSE_TRIAL_STARTED_AT_PATH') ?: storage_path('app/license/trial-started-at.txt'),
    'trial_days' => (int) env('LICENSE_TRIAL_DAYS', 15),
    'clock_skew_minutes' => (int) env('LICENSE_CLOCK_SKEW_MINUTES', 10),
    'public_key' => env('LICENSE_PUBLIC_KEY', <<<'KEY'
-----BEGIN PUBLIC KEY-----
MIIBojANBgkqhkiG9w0BAQEFAAOCAY8AMIIBigKCAYEAzROWCApcKapE4wdLRvkN
mhucxOxph3LkyQQazVH36ZMgMYabjU1cvND1VJ9tcctLKc0UoBaerZRNepRUzse8
SZnRldszr1k3hR+29v+ZhLQXIJyIpQY1I44YoNC2iA6ymlPg/WXiTrZ+5cj7bKzE
ZUwCBNSAq3/FytLEyN8cQjVchlYhwn2BC/RpCD/YSnfUAh7XbzVMYtExm35Glsnu
otycEzE8R1ihbJKj7SXnEDjYeI+wUyIa3tzB009ApJV4ik41kZkARXJcIWgdvWTe
k9Dv8xib9AuFoPfZSGGCXO21jv8/iFcnvbBeGVUOMhGo2HqcdFOpXmLr7a+B6IDd
12EQH2kWsmmSrYTft92cfbs6FSiSoDqaV03PYxkN/TFWyeOscVaz/nqKkseXE1FI
d1g11V5GB6tjFXLQi+KqmqeQTvl2mo5lTyu/j5FamHIZ7RRQa9+jdUOapX4TeRzJ
40pz3LWApsy94NSzSU1X28KB3Yr3InUyChEq5E6u9qipAgMBAAE=
-----END PUBLIC KEY-----
KEY),
];
