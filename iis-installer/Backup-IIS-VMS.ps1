$ErrorActionPreference = "Stop"
$root = Split-Path -Parent $PSScriptRoot
$secrets = Get-Content (Join-Path $root "data\installation-secrets.json") -Raw | ConvertFrom-Json
$timestamp = Get-Date -Format "yyyyMMdd-HHmmss"
$backupDir = Join-Path $root "backups"
$workDir = Join-Path $backupDir "VMS-$timestamp"
New-Item -ItemType Directory -Force -Path $workDir | Out-Null

$dump = Join-Path $root "runtime\mariadb\bin\mariadb-dump.exe"
& $dump `
    --host=127.0.0.1 `
    "--port=$($secrets.database_port)" `
    "--user=$($secrets.database_user)" `
    "--password=$($secrets.database_password)" `
    --single-transaction `
    --routines `
    --events `
    $secrets.database_name |
    Set-Content (Join-Path $workDir "database.sql") -Encoding UTF8

if ($LASTEXITCODE -ne 0) { throw "Sao luu database that bai." }

$uploads = Join-Path $root "data\storage-public"
if (-not (Test-Path $uploads)) {
    $uploads = Join-Path $root "app\storage\app\public"
}
if (Test-Path $uploads) {
    Copy-Item $uploads (Join-Path $workDir "uploads") -Recurse -Force
}
Copy-Item (Join-Path $root "app\.env") (Join-Path $workDir "app.env") -Force

Compress-Archive -Path (Join-Path $workDir "*") -DestinationPath "$workDir.zip" -Force
Remove-Item $workDir -Recurse -Force
Write-Host "Da sao luu: $workDir.zip"
