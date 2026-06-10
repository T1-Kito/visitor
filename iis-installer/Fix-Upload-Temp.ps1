param(
    [string]$InstallPath = "C:\KhachMoiVMS",
    [string]$AppPoolName = "KhachMoiVMS"
)

$ErrorActionPreference = "Stop"

if (-not ([Security.Principal.WindowsPrincipal][Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) {
    throw "Can chay cong cu bang quyen Administrator."
}

$tempPath = Join-Path $InstallPath "temp"
$phpIni = Join-Path $InstallPath "runtime\php\php.ini"

if (-not (Test-Path $phpIni)) {
    throw "Khong tim thay PHP tai $phpIni."
}

New-Item -ItemType Directory -Force -Path $tempPath | Out-Null
$content = Get-Content $phpIni -Raw
$setting = "upload_tmp_dir = `"$tempPath`""

if ($content -match '(?m)^\s*upload_tmp_dir\s*=.*$') {
    $content = $content -replace '(?m)^\s*upload_tmp_dir\s*=.*$', $setting
} else {
    $content = $content.TrimEnd() + "`r`n$setting`r`n"
}

Set-Content -Path $phpIni -Value $content -Encoding ASCII
& icacls $tempPath /grant "IIS AppPool\$($AppPoolName):(OI)(CI)M" /T /C | Out-Null

Import-Module WebAdministration
Restart-WebAppPool -Name $AppPoolName

Write-Host "Da sua cau hinh upload. Hay tai lai trang va thu tai logo."
