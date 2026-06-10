param()

$ErrorActionPreference = "Stop"
$issuerRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$projectRoot = Split-Path -Parent $issuerRoot
$php = Get-Command php -ErrorAction SilentlyContinue

if (-not $php) {
    $bundledPhp = Join-Path $projectRoot "dist\KhachMoi-VMS-IIS\runtime\php\php.exe"
    if (Test-Path -LiteralPath $bundledPhp) {
        $phpPath = $bundledPhp
    } else {
        throw "Khong tim thay PHP. Vui long giu thu muc dist hoac cai PHP."
    }
} else {
    $phpPath = $php.Source
}

$privateKey = Join-Path $issuerRoot "private\license-private.pem"
if (-not (Test-Path -LiteralPath $privateKey)) {
    throw "Khong tim thay private key cap license."
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  CAP BAN QUYEN - KHACH MOI VMS" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

do {
    $deviceId = (Read-Host "Nhap ma may chu").Trim().ToUpper()
    if ($deviceId -notmatch '^VMS-[A-Z0-9]{6}-[A-Z0-9]{6}-[A-Z0-9]{6}-[A-Z0-9]{6}$') {
        Write-Host "Ma may chu khong dung dinh dang. Vui long copy lai tu trang /license." -ForegroundColor Yellow
    }
} while ($deviceId -notmatch '^VMS-[A-Z0-9]{6}-[A-Z0-9]{6}-[A-Z0-9]{6}-[A-Z0-9]{6}$')

do {
    $customer = (Read-Host "Nhap ten khach hang/cong ty").Trim()
    if ($customer -eq "") {
        Write-Host "Ten khach hang khong duoc de trong." -ForegroundColor Yellow
    }
} while ($customer -eq "")

Write-Host ""
Write-Host "Chon thoi han license:" -ForegroundColor DarkGray
Write-Host "  1. 3 thang"
Write-Host "  2. 6 thang"
Write-Host "  3. 9 thang"
Write-Host "  4. 12 thang (mac dinh)"
Write-Host "  5. Nhap ngay het han tuy chon"
Write-Host "  6. Vinh vien"

$expires = ""
$months = "12"
do {
    $choice = (Read-Host "Lua chon").Trim()
    if ($choice -eq "") { $choice = "4" }

    switch ($choice) {
        "1" { $months = "3"; $validChoice = $true }
        "2" { $months = "6"; $validChoice = $true }
        "3" { $months = "9"; $validChoice = $true }
        "4" { $months = "12"; $validChoice = $true }
        "5" {
            $months = ""
            do {
                $expires = (Read-Host "Nhap ngay het han YYYY-MM-DD").Trim()
                $validExpiry = $expires -match '^\d{4}-\d{2}-\d{2}$'
                if ($validExpiry) {
                    try {
                        [void][datetime]::ParseExact($expires, "yyyy-MM-dd", $null)
                    } catch {
                        $validExpiry = $false
                    }
                }
                if (-not $validExpiry) {
                    Write-Host "Ngay het han khong hop le. Vi du: 2027-12-31." -ForegroundColor Yellow
                }
            } while (-not $validExpiry)
            $validChoice = $true
        }
        "6" { $months = ""; $expires = ""; $validChoice = $true }
        default {
            $validChoice = $false
            Write-Host "Vui long chon tu 1 den 6." -ForegroundColor Yellow
        }
    }
} while (-not $validChoice)

$safeCustomer = ($customer -replace '[^\p{L}\p{Nd}-]+', '-').Trim('-').ToLower()
if ($safeCustomer -eq "") {
    $safeCustomer = "khach-hang"
}

$outputDirectory = Join-Path $issuerRoot "licenses"
New-Item -ItemType Directory -Force -Path $outputDirectory | Out-Null
$outputPath = Join-Path $outputDirectory ("license-{0}-{1}.json" -f $safeCustomer, (Get-Date -Format "yyyyMMdd-HHmmss"))
$issuerScript = Join-Path $issuerRoot "issue.php"

$arguments = @(
    $issuerScript
    "--device=$deviceId"
    "--customer=$customer"
    "--private-key=$privateKey"
    "--out=$outputPath"
)

if ($expires -ne "") {
    $arguments += "--expires=$expires"
} elseif ($months -ne "") {
    $arguments += "--months=$months"
}

& $phpPath @arguments
if ($LASTEXITCODE -ne 0) {
    throw "Cap license that bai."
}

Write-Host ""
Write-Host "CAP LICENSE THANH CONG" -ForegroundColor Green
$expiryDisplay = if ($expires -ne "") {
    $expires
} elseif ($months -ne "") {
    "$months thang"
} else {
    "Vinh vien"
}
Write-Host "Khach hang : $customer"
Write-Host "Ma may chu : $deviceId"
Write-Host "Het han    : $expiryDisplay"
Write-Host "File       : $outputPath"
Write-Host ""

Start-Process explorer.exe -ArgumentList "/select,`"$outputPath`""
