$ErrorActionPreference = "Stop"

$BridgeDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$StartupDir = [Environment]::GetFolderPath("Startup")
$ShortcutPath = Join-Path $StartupDir "Gatehouse Printer Bridge.lnk"
$StartScript = Join-Path $BridgeDir "start-printer-bridge.ps1"
$ConfigFile = Join-Path $BridgeDir "config.json"
$ExampleConfigFile = Join-Path $BridgeDir "config.example.json"

Write-Host "Gatehouse Printer Bridge - Cai dat tu dong" -ForegroundColor Cyan
Write-Host "Thu muc bridge: $BridgeDir"

$nodeCommand = Get-Command node -ErrorAction SilentlyContinue
if (-not $nodeCommand) {
    Write-Host ""
    Write-Host "Chua tim thay Node.js." -ForegroundColor Yellow
    Write-Host "Vui long cai Node.js LTS truoc, sau do chay lai file nay:"
    Write-Host "https://nodejs.org/"
    exit 1
}

Write-Host "Node.js: $(& node --version)" -ForegroundColor Green

if (-not (Test-Path -LiteralPath $ConfigFile)) {
    Copy-Item -LiteralPath $ExampleConfigFile -Destination $ConfigFile
    Write-Host "Da tao config.json tu file mau." -ForegroundColor Green
}

$PowerShellPath = "$env:SystemRoot\System32\WindowsPowerShell\v1.0\powershell.exe"
$Shortcut = (New-Object -ComObject WScript.Shell).CreateShortcut($ShortcutPath)
$Shortcut.TargetPath = $PowerShellPath
$Shortcut.Arguments = "-NoProfile -ExecutionPolicy Bypass -WindowStyle Hidden -File `"$StartScript`""
$Shortcut.WorkingDirectory = $BridgeDir
$Shortcut.WindowStyle = 7
$Shortcut.Description = "Gatehouse Printer Bridge - tu dong chay khi dang nhap Windows"
$Shortcut.Save()

Write-Host "Da tao shortcut tu khoi dong cung Windows:" -ForegroundColor Green
Write-Host $ShortcutPath

$AlreadyRunning = Get-CimInstance Win32_Process |
    Where-Object {
        $_.CommandLine -and
        $_.CommandLine.Contains("server.js") -and
        $_.CommandLine.Contains("printer-bridge")
    }

if (-not $AlreadyRunning) {
    Start-Process -FilePath $PowerShellPath `
        -ArgumentList "-NoProfile -ExecutionPolicy Bypass -WindowStyle Hidden -File `"$StartScript`"" `
        -WorkingDirectory $BridgeDir `
        -WindowStyle Hidden
    Start-Sleep -Seconds 2
    Write-Host "Da khoi dong Printer Bridge nen." -ForegroundColor Green
} else {
    Write-Host "Printer Bridge dang chay san." -ForegroundColor Green
}

Write-Host ""
Write-Host "Kiem tra nhanh:" -ForegroundColor Cyan
Write-Host "http://127.0.0.1:9191/health"
Write-Host ""
Write-Host "Buoc tiep theo:"
Write-Host "1. Mo trang admin /settings/printer"
Write-Host "2. Bam Kiem tra ket noi"
Write-Host "3. Chon may in nhiet"
Write-Host "4. Bam Luu cau hinh"
Write-Host "5. Bam In thu QR"
