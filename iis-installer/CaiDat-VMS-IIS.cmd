@echo off
setlocal EnableExtensions EnableDelayedExpansion
cd /d "%~dp0"
set "SCRIPT_DIR=%~dp0"
set "SELF=%~f0"
set "INSTALL_LOG=%TEMP%\KhachMoiVMS-install.log"

if /i not "%~1"=="elevated" (
    net session >nul 2>&1
    if errorlevel 1 (
        powershell -NoProfile -ExecutionPolicy Bypass -Command "Start-Process -FilePath '!SELF!' -ArgumentList 'elevated' -Verb RunAs"
        if errorlevel 1 (
            echo [LOI] Khong mo duoc quyen Administrator.
            echo Vui long chuot phai CaiDat-VMS-IIS.cmd va chon Run as administrator.
            echo.
            pause
        )
        exit /b
    )
)

net session >nul 2>&1
if errorlevel 1 (
    echo [LOI] File cai dat can chay bang quyen Administrator.
    echo Chuot phai CaiDat-VMS-IIS.cmd va chon Run as administrator.
    echo.
    pause
    exit /b
)

title Cai dat Khach Moi VMS tren IIS
echo.
echo ==========================================
echo   CAI DAT KHACH MOI VMS - IIS + MARIADB
echo ==========================================
echo.
echo Log cai dat: %INSTALL_LOG%
echo Bat dau cai dat luc %DATE% %TIME% > "%INSTALL_LOG%"
echo Thu muc bo cai: !SCRIPT_DIR! >> "%INSTALL_LOG%"
echo.

set "INSTALL_SCRIPT=!SCRIPT_DIR!tools\Install-IIS-VMS.ps1"

if not exist "!INSTALL_SCRIPT!" (
    for /f "delims=" %%I in ('dir /b /s "!SCRIPT_DIR!Install-IIS-VMS.ps1" 2^>nul') do (
        set "INSTALL_SCRIPT=%%I"
        goto :found_script
    )
)

:found_script
if not exist "!INSTALL_SCRIPT!" (
    echo [LOI] Khong tim thay file tools\Install-IIS-VMS.ps1.
    echo Vui long giai nen TOAN BO file ZIP roi chay lai CaiDat-VMS-IIS.cmd.
    echo Khong chay truc tiep ben trong file ZIP.
    echo.
    echo Thu muc hien tai:
    echo !SCRIPT_DIR!
    echo.
    echo Trong thu muc nay phai co:
    echo - app
    echo - runtime
    echo - tools
    echo - CaiDat-VMS-IIS.cmd
    echo.
    pause
    exit /b 1
)

powershell -NoProfile -ExecutionPolicy Bypass -File "!INSTALL_SCRIPT!" 1>>"%INSTALL_LOG%" 2>>&1
if errorlevel 1 (
    echo.
    echo [LOI] Cai dat chua thanh cong. Vui long chup man hinh gui ky thuat.
    echo Xem log chi tiet tai:
    echo %INSTALL_LOG%
    echo.
    type "%INSTALL_LOG%"
    echo.
    pause
    exit /b 1
)

echo.
echo Cai dat xong. Shortcut da duoc tao ngoai Desktop.
pause
