$ErrorActionPreference = "Stop"

$StartupDir = [Environment]::GetFolderPath("Startup")
$ShortcutPath = Join-Path $StartupDir "Gatehouse Printer Bridge.lnk"

Write-Host "Gatehouse Printer Bridge - Go cai dat" -ForegroundColor Cyan

if (Test-Path -LiteralPath $ShortcutPath) {
    Remove-Item -LiteralPath $ShortcutPath -Force
    Write-Host "Da xoa shortcut tu khoi dong cung Windows." -ForegroundColor Green
} else {
    Write-Host "Khong tim thay shortcut trong Startup." -ForegroundColor Yellow
}

$Processes = Get-CimInstance Win32_Process |
    Where-Object {
        $_.CommandLine -and
        $_.CommandLine.Contains("server.js") -and
        $_.CommandLine.Contains("printer-bridge")
    }

foreach ($Process in $Processes) {
    Stop-Process -Id $Process.ProcessId -Force -ErrorAction SilentlyContinue
}

if ($Processes) {
    Write-Host "Da dung Printer Bridge dang chay." -ForegroundColor Green
}

Write-Host "Hoan tat."
