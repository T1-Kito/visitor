param(
    [string]$InstallPath = "C:\KhachMoiVMS",
    [int]$WebPort = 8080,
    [int]$DatabasePort = 3307,
    [string]$AdminEmail = "admin@company.local",
    [string]$AdminPassword = "Admin@123"
)

$ErrorActionPreference = "Stop"
$packageRoot = Split-Path -Parent $PSScriptRoot
$sourceApp = Join-Path $packageRoot "app"
$sourceRuntime = Join-Path $packageRoot "runtime"
$appPath = Join-Path $InstallPath "app"
$phpPath = Join-Path $InstallPath "runtime\php"
$mariaPath = Join-Path $InstallPath "runtime\mariadb"
$dataPath = Join-Path $InstallPath "data\mariadb"
$persistentUploadsPath = Join-Path $InstallPath "data\storage-public"
$licenseDataPath = Join-Path $InstallPath "data\license"
$licenseDeviceIdPath = Join-Path $licenseDataPath "device-id.txt"
$licenseStoragePath = Join-Path $licenseDataPath "license.json"
$licenseTrialStartedAtPath = Join-Path $licenseDataPath "trial-started-at.txt"
$logsPath = Join-Path $InstallPath "logs"
$tempPath = Join-Path $InstallPath "temp"
$secretsPath = Join-Path $InstallPath "data\installation-secrets.json"
$siteName = "KhachMoiVMS"
$appPoolName = "KhachMoiVMS"
$databaseService = "KhachMoiVMS-DB"

if (-not ([Security.Principal.WindowsPrincipal][Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) {
    throw "Can chay bo cai bang quyen Administrator."
}

foreach ($required in @(
    (Join-Path $sourceRuntime "php\php-cgi.exe"),
    (Join-Path $sourceRuntime "mariadb\bin\mariadb-install-db.exe"),
    (Join-Path $sourceRuntime "rewrite_amd64.msi")
)) {
    if (-not (Test-Path $required)) {
        throw "Bo cai thieu runtime: $required"
    }
}

Write-Host "Dang bat IIS va CGI..."
Enable-WindowsOptionalFeature -Online -FeatureName `
    IIS-WebServerRole, IIS-WebServer, IIS-CommonHttpFeatures, IIS-StaticContent, `
    IIS-DefaultDocument, IIS-HttpErrors, IIS-ApplicationDevelopment, IIS-CGI, `
    IIS-ISAPIExtensions, IIS-ISAPIFilter, IIS-ManagementConsole, `
    IIS-ManagementScriptingTools `
    -All -NoRestart | Out-Null
Import-Module WebAdministration -ErrorAction Stop

Write-Host "Dang dung ban cai cu neu co..."
if (Test-Path "IIS:\Sites\$siteName") {
    Stop-Website -Name $siteName -ErrorAction SilentlyContinue
}
if (Test-Path "IIS:\AppPools\$appPoolName") {
    Stop-WebAppPool -Name $appPoolName -ErrorAction SilentlyContinue
}
if (Get-Service $databaseService -ErrorAction SilentlyContinue) {
    Stop-Service $databaseService -Force -ErrorAction SilentlyContinue
}
Get-Process mariadbd, mysql, php-cgi -ErrorAction SilentlyContinue |
    Where-Object { $_.Path -like "$InstallPath*" } |
    Stop-Process -Force -ErrorAction SilentlyContinue
Start-Sleep -Seconds 2

$vcRedist = Join-Path $sourceRuntime "VC_redist.x64.exe"
if (Test-Path $vcRedist) {
    $vc = Start-Process $vcRedist -ArgumentList "/install", "/quiet", "/norestart" -Wait -PassThru
    if ($vc.ExitCode -notin @(0, 1638, 3010)) {
        throw "Khong cai duoc Microsoft Visual C++ Runtime. Ma loi $($vc.ExitCode)."
    }
}

Write-Host "Dang cai IIS URL Rewrite..."
$rewrite = Start-Process msiexec.exe -ArgumentList "/i", "`"$(Join-Path $sourceRuntime 'rewrite_amd64.msi')`"", "/qn", "/norestart" -Wait -PassThru
if ($rewrite.ExitCode -notin @(0, 1638, 3010)) {
    throw "Khong cai duoc IIS URL Rewrite. Ma loi $($rewrite.ExitCode)."
}

Write-Host "Dang sao chep VMS..."
$existingEnvPath = Join-Path $appPath ".env"
$existingEnvContent = if (Test-Path $existingEnvPath) { Get-Content $existingEnvPath -Raw } else { $null }
$legacyUploadsPath = Join-Path $appPath "storage\app\public"
$publicStorageLink = Join-Path $appPath "public\storage"

New-Item -ItemType Directory -Force -Path $InstallPath, $logsPath, $tempPath, $persistentUploadsPath, $licenseDataPath, (Split-Path $dataPath -Parent) | Out-Null
if (-not (Test-Path $licenseDeviceIdPath)) {
    $deviceChars = ([Guid]::NewGuid().ToString("N").Substring(0, 24)).ToUpper()
    $deviceCode = "VMS-" + ($deviceChars.Substring(0, 6)) + "-" + ($deviceChars.Substring(6, 6)) + "-" + ($deviceChars.Substring(12, 6)) + "-" + ($deviceChars.Substring(18, 6))
    $deviceCode | Set-Content $licenseDeviceIdPath -Encoding ASCII
}
if (-not (Test-Path $licenseTrialStartedAtPath)) {
    (Get-Date -Format "yyyy-MM-dd") | Set-Content $licenseTrialStartedAtPath -Encoding ASCII
}
if (Test-Path $legacyUploadsPath) {
    Write-Host "Dang bao toan logo va file da tai len..."
    Get-ChildItem -Path $legacyUploadsPath -Force -ErrorAction SilentlyContinue |
        Copy-Item -Destination $persistentUploadsPath -Recurse -Force
}
if (Test-Path $publicStorageLink) {
    $storageLinkItem = Get-Item -LiteralPath $publicStorageLink -Force
    if (($storageLinkItem.Attributes -band [IO.FileAttributes]::ReparsePoint) -ne 0) {
        [IO.Directory]::Delete($publicStorageLink)
    }
}
foreach ($folder in @("app", "runtime", "tools")) {
    $source = Join-Path $packageRoot $folder
    $target = Join-Path $InstallPath $folder
    if (Test-Path $target) {
        if ($folder -eq "runtime" -and (Get-Service $databaseService -ErrorAction SilentlyContinue)) {
            $runtimeBackup = Join-Path $InstallPath ("runtime-old-" + (Get-Date -Format "yyyyMMddHHmmss"))
            Rename-Item -Path $target -NewName (Split-Path $runtimeBackup -Leaf) -Force
        } else {
            Remove-Item $target -Recurse -Force
        }
    }
    Copy-Item $source $target -Recurse -Force
}

Copy-Item (Join-Path $InstallPath "tools\php.ini") (Join-Path $phpPath "php.ini") -Force
Add-Content (Join-Path $phpPath "php.ini") "`r`nupload_tmp_dir = `"$tempPath`""

$databaseName = "vms"
$databaseUser = "vms_app"
$existingSecrets = if (Test-Path $secretsPath) {
    Get-Content $secretsPath -Raw | ConvertFrom-Json
} else {
    $null
}

if ($existingSecrets) {
    $rootPassword = [string] $existingSecrets.database_root_password
    $databasePassword = [string] $existingSecrets.database_password
    $databaseName = [string] $existingSecrets.database_name
    $databaseUser = [string] $existingSecrets.database_user
    $DatabasePort = [int] $existingSecrets.database_port
} else {
    $rootPasswordBytes = New-Object byte[] 24
    $databasePasswordBytes = New-Object byte[] 24
    [Security.Cryptography.RandomNumberGenerator]::Create().GetBytes($rootPasswordBytes)
    [Security.Cryptography.RandomNumberGenerator]::Create().GetBytes($databasePasswordBytes)
    $rootPassword = [Convert]::ToBase64String($rootPasswordBytes)
    $databasePassword = [Convert]::ToBase64String($databasePasswordBytes)
}

if (-not (Get-Service $databaseService -ErrorAction SilentlyContinue)) {
    Write-Host "Dang khoi tao MariaDB..."
    & (Join-Path $mariaPath "bin\mariadb-install-db.exe") `
        "--datadir=$dataPath" `
        "--service=$databaseService" `
        "--password=$rootPassword" `
        "--port=$DatabasePort"
    if ($LASTEXITCODE -ne 0) { throw "Khong khoi tao duoc MariaDB." }
}

Set-Service $databaseService -StartupType Automatic
Start-Service $databaseService

$mariaClient = Join-Path $mariaPath "bin\mariadb.exe"
$sql = @"
CREATE DATABASE IF NOT EXISTS ``$databaseName`` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$databaseUser'@'127.0.0.1' IDENTIFIED BY '$databasePassword';
ALTER USER '$databaseUser'@'127.0.0.1' IDENTIFIED BY '$databasePassword';
GRANT ALL PRIVILEGES ON ``$databaseName``.* TO '$databaseUser'@'127.0.0.1';
FLUSH PRIVILEGES;
"@
$sql | & $mariaClient --host=127.0.0.1 --port=$DatabasePort --user=root "--password=$rootPassword"
if ($LASTEXITCODE -ne 0) { throw "Khong tao duoc database VMS." }

$existingAppKeyMatch = if ($existingEnvContent) {
    [regex]::Match($existingEnvContent, '(?m)^APP_KEY=(.+)$')
} else {
    $null
}
if ($existingAppKeyMatch -and $existingAppKeyMatch.Success) {
    $appKey = $existingAppKeyMatch.Groups[1].Value.Trim()
} else {
    $keyBytes = New-Object byte[] 32
    [Security.Cryptography.RandomNumberGenerator]::Create().GetBytes($keyBytes)
    $appKey = "base64:$([Convert]::ToBase64String($keyBytes))"
}
$hostIp = Get-NetIPAddress -AddressFamily IPv4 -ErrorAction SilentlyContinue |
    Where-Object {
        ($_.IPAddress -notlike "127.*") -and
        ($_.IPAddress -notlike "169.254.*") -and
        ($_.InterfaceAlias -notmatch "Loopback|Virtual|VMware|Hyper-V|vEthernet|WSL|VPN")
    } |
    Select-Object -First 1 -ExpandProperty IPAddress
$appUrl = "http://localhost:$WebPort"

$envTemplate = Get-Content (Join-Path $InstallPath "tools\app.env.template") -Raw
$envContent = $envTemplate.Replace("{{APP_KEY}}", $appKey)
$envContent = $envContent.Replace("{{APP_URL}}", $appUrl)
$envContent = $envContent.Replace("{{DB_PORT}}", [string]$DatabasePort)
$envContent = $envContent.Replace("{{DB_DATABASE}}", $databaseName)
$envContent = $envContent.Replace("{{DB_USERNAME}}", $databaseUser)
$envContent = $envContent.Replace("{{DB_PASSWORD}}", $databasePassword)
$envContent = $envContent.Replace("{{PUBLIC_STORAGE_PATH}}", $persistentUploadsPath.Replace("\", "/"))
$envContent = $envContent.Replace("{{LICENSE_DEVICE_ID_PATH}}", $licenseDeviceIdPath.Replace("\", "/"))
$envContent = $envContent.Replace("{{LICENSE_STORAGE_PATH}}", $licenseStoragePath.Replace("\", "/"))
$envContent = $envContent.Replace("{{LICENSE_TRIAL_STARTED_AT_PATH}}", $licenseTrialStartedAtPath.Replace("\", "/"))
$envContent = $envContent.Replace("{{ADMIN_EMAIL}}", $AdminEmail)
$envContent = $envContent.Replace("{{ADMIN_PASSWORD}}", $AdminPassword)
$envContent | Set-Content (Join-Path $appPath ".env") -Encoding UTF8

$php = Join-Path $phpPath "php.exe"
Push-Location $appPath
try {
    & $php artisan migrate --force
    if ($LASTEXITCODE -ne 0) { throw "Migration database that bai." }
    & $php artisan db:seed --class=AdminSeeder --force
    if ($LASTEXITCODE -ne 0) { throw "Khong tao duoc tai khoan admin." }
    & $php artisan storage:link
    & $php artisan optimize
} finally {
    Pop-Location
}

Write-Host "Dang cau hinh IIS FastCGI..."
Import-Module WebAdministration

if (Test-Path "IIS:\Sites\$siteName") {
    Remove-Website -Name $siteName
}
if (Test-Path "IIS:\AppPools\$appPoolName") {
    Remove-WebAppPool -Name $appPoolName
}

New-WebAppPool -Name $appPoolName | Out-Null
Set-ItemProperty "IIS:\AppPools\$appPoolName" -Name managedRuntimeVersion -Value ""
Set-ItemProperty "IIS:\AppPools\$appPoolName" -Name processModel.identityType -Value ApplicationPoolIdentity
Set-ItemProperty "IIS:\AppPools\$appPoolName" -Name startMode -Value AlwaysRunning

$phpCgi = Join-Path $phpPath "php-cgi.exe"
$appcmd = "$env:windir\System32\inetsrv\appcmd.exe"
& $appcmd set config /section:system.webServer/fastCgi "/+[fullPath='$phpCgi',maxInstances='8',instanceMaxRequests='10000',activityTimeout='120',requestTimeout='120']" /commit:apphost 2>$null
& $appcmd set config /section:system.webServer/handlers "/+[name='PHP_via_FastCGI',path='*.php',verb='GET,HEAD,POST,PUT,DELETE,PATCH,OPTIONS',modules='FastCgiModule',scriptProcessor='$phpCgi',resourceType='Either',requireAccess='Script']" /commit:apphost 2>$null

New-Website -Name $siteName -Port $WebPort -PhysicalPath (Join-Path $appPath "public") -ApplicationPool $appPoolName | Out-Null

& icacls (Join-Path $appPath "storage") /grant "IIS AppPool\$($appPoolName):(OI)(CI)M" /T /C | Out-Null
& icacls (Join-Path $appPath "bootstrap\cache") /grant "IIS AppPool\$($appPoolName):(OI)(CI)M" /T /C | Out-Null
& icacls $tempPath /grant "IIS AppPool\$($appPoolName):(OI)(CI)M" /T /C | Out-Null
& icacls $licenseDataPath /grant "IIS AppPool\$($appPoolName):(OI)(CI)M" /T /C | Out-Null

$schedulerAction = "powershell.exe -NoProfile -WindowStyle Hidden -ExecutionPolicy Bypass -File `"$InstallPath\tools\Run-Scheduler.ps1`""
schtasks /Create /TN "KhachMoiVMS-Scheduler" /SC MINUTE /MO 5 /RU SYSTEM /RL HIGHEST /TR $schedulerAction /F | Out-Null

netsh advfirewall firewall delete rule name="Khach Moi VMS" | Out-Null
netsh advfirewall firewall add rule name="Khach Moi VMS" dir=in action=allow protocol=TCP localport=$WebPort | Out-Null

$desktop = [Environment]::GetFolderPath("CommonDesktopDirectory")
@(
    "[InternetShortcut]"
    "URL=http://localhost:$WebPort"
    "IconFile=$appPath\public\icons\vms-shortcut.ico"
    "IconIndex=0"
) | Set-Content (Join-Path $desktop "Khach Moi VMS.url") -Encoding ASCII

$secrets = @{
    database_port = $DatabasePort
    database_name = $databaseName
    database_user = $databaseUser
    database_password = $databasePassword
    database_root_password = $rootPassword
} | ConvertTo-Json
$secrets | Set-Content (Join-Path $InstallPath "data\installation-secrets.json") -Encoding UTF8

Start-Website -Name $siteName
Start-Process "http://localhost:$WebPort"

Write-Host ""
Write-Host "Cai dat thanh cong."
Write-Host "May chu: http://localhost:$WebPort"
if ($hostIp) { Write-Host "May con goi y: http://${hostIp}:$WebPort" }
Write-Host "Tai khoan: $AdminEmail"
