$ErrorActionPreference = "Stop"
$root = Split-Path -Parent $PSScriptRoot
& (Join-Path $root "runtime\php\php.exe") (Join-Path $root "app\artisan") schedule:run --no-interaction
