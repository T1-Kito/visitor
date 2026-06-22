<?php

return [
    'enabled' => (bool) env('PUBLIC_REGISTRATION_ENABLED', true),
    'port' => (int) env('PUBLIC_REGISTRATION_PORT', 8443),
];