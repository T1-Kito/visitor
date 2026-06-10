@echo off
setlocal

net session >nul 2>&1
if errorlevel 1 (
    powershell -NoProfile -Command "Start-Process -FilePath '%~f0' -Verb RunAs"
    exit /b
)

set "FIX_SCRIPT=%~dp0tools\Fix-AppUrl.ps1"
if not exist "%FIX_SCRIPT%" set "FIX_SCRIPT=C:\KhachMoiVMS\tools\Fix-AppUrl.ps1"

if not exist "%FIX_SCRIPT%" (
    echo [LOI] Khong tim thay tools\Fix-AppUrl.ps1.
    echo Vui long giai nen TOAN BO file ZIP roi chay lai Sua-APP-URL.cmd.
    pause
    exit /b 1
)

powershell -NoProfile -ExecutionPolicy Bypass -File "%FIX_SCRIPT%"
pause
