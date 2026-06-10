@echo off
setlocal
cd /d "%~dp0.."

where php >nul 2>nul
if not errorlevel 1 goto php_found

set "PHP_EXE=%CD%\dist\KhachMoi-VMS-IIS\runtime\php\php.exe"
if not exist "%PHP_EXE%" (
    echo [LOI] Khong tim thay PHP.
    pause
    exit /b 1
)
goto start_portal

:php_found
set "PHP_EXE=php"

:start_portal
start "" powershell.exe -NoProfile -WindowStyle Hidden -Command "Start-Sleep -Seconds 2; Start-Process 'http://127.0.0.1:8799'"
"%PHP_EXE%" -S 127.0.0.1:8799 license-issuer\portal.php
