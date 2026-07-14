param(
    [string]$OutputDirectory = ".\dist",
    [string]$PackageName = "KhachMoi-VMS-IIS",
    [string]$PhpUrl = "https://windows.php.net/downloads/releases/latest/php-8.3-nts-Win32-vs16-x64-latest.zip",
    [string]$MariaDbUrl = "https://archive.mariadb.org/mariadb-11.4.11/winx64-packages/mariadb-11.4.11-winx64.zip",
    [string]$RewriteUrl = "https://download.microsoft.com/download/1/2/8/128E2E22-C1B9-44A4-BE2A-5859ED1D4592/rewrite_amd64_en-US.msi",
    [string]$VcRedistUrl = "https://aka.ms/vs/17/release/vc_redist.x64.exe"
)

$ErrorActionPreference = "Stop"
$root = Split-Path -Parent $MyInvocation.MyCommand.Path
$outputRoot = [IO.Path]::GetFullPath((Join-Path $root $OutputDirectory))
$projectRoot = [IO.Path]::GetFullPath($root)
$packageRoot = Join-Path $outputRoot $PackageName
$zipPath = Join-Path $outputRoot "$PackageName.zip"

if (-not $outputRoot.StartsWith($projectRoot, [StringComparison]::OrdinalIgnoreCase)) {
    throw "Thu muc output phai nam trong project."
}

New-Item -ItemType Directory -Force -Path $outputRoot | Out-Null
if (Test-Path $packageRoot) { Remove-Item $packageRoot -Recurse -Force }
if (Test-Path $zipPath) { Remove-Item $zipPath -Force }

$appTarget = Join-Path $packageRoot "app"
$runtimeTarget = Join-Path $packageRoot "runtime"
$toolsTarget = Join-Path $packageRoot "tools"
New-Item -ItemType Directory -Force -Path $appTarget, $runtimeTarget, $toolsTarget | Out-Null

Write-Host "Dang sao chep source production..."
foreach ($folder in @("app", "bootstrap", "config", "database", "resources", "routes")) {
    Copy-Item (Join-Path $root $folder) (Join-Path $appTarget $folder) -Recurse -Force
}
Copy-Item (Join-Path $root "public") (Join-Path $appTarget "public") -Recurse -Force
$publicStorage = Join-Path $appTarget "public\storage"
if (Test-Path $publicStorage) { Remove-Item $publicStorage -Recurse -Force }

foreach ($file in @("artisan", "composer.json", "composer.lock")) {
    Copy-Item (Join-Path $root $file) (Join-Path $appTarget $file) -Force
}

New-Item -ItemType Directory -Force -Path `
    (Join-Path $appTarget "storage\app\public"), `
    (Join-Path $appTarget "storage\framework\cache\data"), `
    (Join-Path $appTarget "storage\framework\sessions"), `
    (Join-Path $appTarget "storage\framework\views"), `
    (Join-Path $appTarget "storage\logs") | Out-Null

# Khong mang cache package discovery tu may phat trien vao ban production.
# Cac file nay co the tham chieu package dev (vi du Laravel Pail) da bi loai bo boi --no-dev.
Get-ChildItem (Join-Path $appTarget "bootstrap\cache") -Filter "*.php" -ErrorAction SilentlyContinue |
    Remove-Item -Force

Write-Host "Dang chuan bi vendor production..."
if (Get-Command composer -ErrorAction SilentlyContinue) {
    Push-Location $appTarget
    try {
        composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader
        if ($LASTEXITCODE -ne 0) { throw "Composer install that bai." }
    } finally {
        Pop-Location
    }
} elseif (Test-Path (Join-Path $root "vendor")) {
    Copy-Item (Join-Path $root "vendor") (Join-Path $appTarget "vendor") -Recurse -Force
} else {
    throw "Khong tim thay Composer hoac thu muc vendor."
}

Write-Host "Dang tai PHP NTS cho IIS FastCGI..."
$phpZip = Join-Path $env:TEMP "vms-iis-php.zip"
Invoke-WebRequest -Uri $PhpUrl -OutFile $phpZip
$phpTarget = Join-Path $runtimeTarget "php"
New-Item -ItemType Directory -Force -Path $phpTarget | Out-Null
Expand-Archive $phpZip $phpTarget -Force
Copy-Item (Join-Path $root "iis-installer\php.ini") (Join-Path $phpTarget "php.ini") -Force

Write-Host "Dang tai MariaDB..."
$mariaZip = Join-Path $env:TEMP "vms-iis-mariadb.zip"
$mariaExtract = Join-Path $env:TEMP "vms-iis-mariadb"
if (Test-Path $mariaExtract) { Remove-Item $mariaExtract -Recurse -Force }
Invoke-WebRequest -Uri $MariaDbUrl -OutFile $mariaZip
Expand-Archive $mariaZip $mariaExtract -Force
$mariaSource = Get-ChildItem $mariaExtract -Directory | Select-Object -First 1
if (-not $mariaSource) { throw "Khong giai nen duoc MariaDB." }
Copy-Item $mariaSource.FullName (Join-Path $runtimeTarget "mariadb") -Recurse -Force

Write-Host "Dang tai IIS URL Rewrite va Visual C++ Runtime..."
Invoke-WebRequest -Uri $RewriteUrl -OutFile (Join-Path $runtimeTarget "rewrite_amd64.msi")
Invoke-WebRequest -Uri $VcRedistUrl -OutFile (Join-Path $runtimeTarget "VC_redist.x64.exe")

Write-Host "Dang sao chep cong cu cai dat..."
foreach ($file in @(
    "Install-IIS-VMS.ps1",
    "Run-Scheduler.ps1",
    "Backup-IIS-VMS.ps1",
    "Fix-Upload-Temp.ps1",
    "Fix-Logo-Storage.ps1",
    "app.env.template",
    "php.ini"
)) {
    Copy-Item (Join-Path $root "iis-installer\$file") (Join-Path $toolsTarget $file) -Force
}
foreach ($file in @("CaiDat-VMS-IIS.cmd", "SaoLuu-VMS.cmd", "Sua-Loi-Upload.cmd", "Sua-Loi-Logo.cmd")) {
    Copy-Item (Join-Path $root "iis-installer\$file") (Join-Path $packageRoot $file) -Force
}

@"
KHACH MOI VMS - IIS + MARIADB

Yeu cau:
- Windows 10/11 Pro hoac Windows Server 2019 tro len.
- Tai khoan Administrator.
- O dia con it nhat 3 GB.

Cai dat:
1. Giai nen file ZIP vao o dia.
2. Bam chuot phai CaiDat-VMS-IIS.cmd.
3. Chon Run as administrator.
4. Cho bo cai bat IIS, cai MariaDB va tao website.

Dia chi tren may chu:
http://localhost:8080

Tai khoan mac dinh:
admin@company.local
Admin@123

May nhan vien truy cap:
http://IP-MAY-CHU:8080

Database MariaDB chi nghe tai 127.0.0.1, may con khong truy cap database truc tiep.
Sao luu bang file SaoLuu-VMS.cmd.
"@ | Set-Content (Join-Path $packageRoot "HUONG-DAN.txt") -Encoding UTF8

Write-Host "Dang kiem tra runtime..."
if (-not (Test-Path (Join-Path $phpTarget "php-cgi.exe"))) { throw "PHP FastCGI bi thieu." }
if (-not (Test-Path (Join-Path $runtimeTarget "mariadb\bin\mariadb-install-db.exe"))) { throw "MariaDB runtime bi thieu." }

Write-Host "Dang nen bo cai..."
Compress-Archive -Path (Join-Path $packageRoot "*") -DestinationPath $zipPath -CompressionLevel Optimal
$sizeMb = [math]::Round((Get-Item $zipPath).Length / 1MB, 1)

Write-Host ""
Write-Host "Dong goi thanh cong:"
Write-Host $zipPath
Write-Host "Dung luong: $sizeMb MB"
