param(
    [string]$InstallPath = "C:\KhachMoiVMS"
)

$ErrorActionPreference = "Stop"

if (-not ([Security.Principal.WindowsPrincipal][Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) {
    throw "Can chay cong cu bang quyen Administrator."
}

$appPath = Join-Path $InstallPath "app"
$dataPath = Join-Path $InstallPath "data"
$persistentUploadsPath = Join-Path $dataPath "storage-public"
$legacyUploadsPath = Join-Path $appPath "storage\app\public"
$publicStorageLink = Join-Path $appPath "public\storage"
$envPath = Join-Path $appPath ".env"
$filesystemsConfig = Join-Path $appPath "config\filesystems.php"
$secretsPath = Join-Path $dataPath "installation-secrets.json"
$php = Join-Path $InstallPath "runtime\php\php.exe"
$maria = Join-Path $InstallPath "runtime\mariadb\bin\mariadb.exe"

foreach ($required in @($envPath, $filesystemsConfig, $secretsPath, $php, $maria)) {
    if (-not (Test-Path $required)) {
        throw "Khong tim thay file bat buoc: $required"
    }
}

New-Item -ItemType Directory -Force -Path $persistentUploadsPath | Out-Null
if (Test-Path $legacyUploadsPath) {
    Get-ChildItem -Path $legacyUploadsPath -Force -ErrorAction SilentlyContinue |
        Copy-Item -Destination $persistentUploadsPath -Recurse -Force
}

$envContent = Get-Content $envPath -Raw
$pathLine = 'PUBLIC_STORAGE_PATH="' + $persistentUploadsPath.Replace("\", "/") + '"'
$urlLine = 'PUBLIC_STORAGE_URL=/storage'

if ($envContent -match '(?m)^PUBLIC_STORAGE_PATH=.*$') {
    $envContent = $envContent -replace '(?m)^PUBLIC_STORAGE_PATH=.*$', $pathLine
} else {
    $envContent = $envContent.TrimEnd() + "`r`n$pathLine`r`n"
}

if ($envContent -match '(?m)^PUBLIC_STORAGE_URL=.*$') {
    $envContent = $envContent -replace '(?m)^PUBLIC_STORAGE_URL=.*$', $urlLine
} else {
    $envContent = $envContent.TrimEnd() + "`r`n$urlLine`r`n"
}

Set-Content -Path $envPath -Value $envContent -Encoding UTF8

$configContent = Get-Content $filesystemsConfig -Raw
$configContent = $configContent.Replace("'root' => storage_path('app/public'),", "'root' => env('PUBLIC_STORAGE_PATH', storage_path('app/public')),")
$configContent = $configContent.Replace("'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',", "'url' => rtrim(env('PUBLIC_STORAGE_URL', '/storage'), '/'),")
$configContent = $configContent.Replace("public_path('storage') => storage_path('app/public'),", "public_path('storage') => env('PUBLIC_STORAGE_PATH', storage_path('app/public')),")
Set-Content -Path $filesystemsConfig -Value $configContent -Encoding UTF8

if (Test-Path $publicStorageLink) {
    $linkItem = Get-Item -LiteralPath $publicStorageLink -Force
    if (($linkItem.Attributes -band [IO.FileAttributes]::ReparsePoint) -ne 0) {
        [IO.Directory]::Delete($publicStorageLink)
    } else {
        throw "Duong dan public\storage khong phai lien ket. Vui long sao luu roi xoa thu cong neu can."
    }
}

$secrets = Get-Content $secretsPath -Raw | ConvertFrom-Json
$assetKeys = @(
    "admin.logo_url",
    "login.logo_url",
    "kiosk.owner_logo_url",
    "kiosk.customer_logo_url",
    "kiosk.logo_url",
    "kiosk.background_url",
    "app.favicon_url"
)
$quotedKeys = ($assetKeys | ForEach-Object { "'$_'" }) -join ","
$selectSql = "SELECT ``key``, ``value`` FROM system_settings WHERE ``key`` IN ($quotedKeys) AND ``value`` LIKE '%/storage/%';"
$assetRows = & $maria `
    --host=127.0.0.1 `
    "--port=$($secrets.database_port)" `
    "--user=$($secrets.database_user)" `
    "--password=$($secrets.database_password)" `
    --batch `
    --skip-column-names `
    $secrets.database_name `
    -e $selectSql

foreach ($row in $assetRows) {
    $parts = $row -split "`t", 2
    if ($parts.Count -lt 2) {
        continue
    }

    $path = [string] $parts[1]
    $storageIndex = $path.IndexOf('/storage/', [StringComparison]::OrdinalIgnoreCase)
    if ($storageIndex -lt 0) {
        continue
    }

    $relative = $path.Substring($storageIndex + '/storage/'.Length).Replace('/', '\')
    $targetFile = Join-Path $persistentUploadsPath $relative
    if (Test-Path $targetFile) {
        continue
    }

    $fileName = Split-Path $relative -Leaf
    $found = Get-ChildItem -Path $InstallPath -Filter $fileName -Recurse -File -ErrorAction SilentlyContinue |
        Where-Object { $_.FullName -notlike "$persistentUploadsPath*" } |
        Select-Object -First 1

    if ($found) {
        New-Item -ItemType Directory -Force -Path (Split-Path $targetFile -Parent) | Out-Null
        Copy-Item -LiteralPath $found.FullName -Destination $targetFile -Force
        Write-Host "Da khoi phuc file logo: $fileName"
    }
}

$sql = @"
UPDATE system_settings
SET ``value`` = SUBSTRING(``value``, LOCATE('/storage/', ``value``))
WHERE ``key`` IN ($quotedKeys)
  AND ``value`` LIKE '%/storage/%';
"@

$sql | & $maria `
    --host=127.0.0.1 `
    "--port=$($secrets.database_port)" `
    "--user=$($secrets.database_user)" `
    "--password=$($secrets.database_password)" `
    $secrets.database_name
if ($LASTEXITCODE -ne 0) {
    throw "Khong sua duoc duong dan logo trong database."
}

Push-Location $appPath
try {
    & $php artisan optimize:clear
    & $php artisan storage:link
    if ($LASTEXITCODE -ne 0) {
        throw "Khong tao duoc lien ket public\storage."
    }
    & $php artisan optimize
} finally {
    Pop-Location
}

Write-Host "Da sua logo/storage. Hay tai lai trinh duyet bang Ctrl+F5."
