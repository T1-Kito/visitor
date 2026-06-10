param(
    [int]$WebPort = 8080
)

$ErrorActionPreference = "Stop"
$envPath = "C:\KhachMoiVMS\app\.env"

if (-not (Test-Path $envPath)) {
    throw "Khong tim thay file $envPath. Co the VMS chua cai xong."
}

$content = Get-Content $envPath
$newUrl = "APP_URL=http://localhost:$WebPort"

if ($content -match '^APP_URL=') {
    $content = $content | ForEach-Object {
        if ($_ -match '^APP_URL=') { $newUrl } else { $_ }
    }
} else {
    $content = @($content) + $newUrl
}

$content | Set-Content $envPath -Encoding UTF8

$php = "C:\KhachMoiVMS\runtime\php\php.exe"
$artisan = "C:\KhachMoiVMS\app\artisan"

& $php $artisan config:clear
& $php $artisan config:cache
iisreset | Out-Null

Write-Host "Da doi APP_URL ve http://localhost:$WebPort"
Write-Host "May con vao bang IP that cua may chu, vi du: http://192.168.110.153:$WebPort"
