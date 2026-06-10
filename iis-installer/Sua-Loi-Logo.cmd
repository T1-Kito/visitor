@echo off
chcp 65001 >nul
title Sua loi logo qua IP LAN - Khach Moi VMS
set "FIX_SCRIPT=%~dp0tools\Fix-Logo-Storage.ps1"
if not exist "%FIX_SCRIPT%" set "FIX_SCRIPT=%~dp0Fix-Logo-Storage.ps1"
if not exist "%FIX_SCRIPT%" (
    echo [LOI] Khong tim thay Fix-Logo-Storage.ps1.
    echo Hay copy kem thu muc tools hoac file Fix-Logo-Storage.ps1 vao cung thu muc voi file nay.
    pause
    exit /b 1
)
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%FIX_SCRIPT%"
if errorlevel 1 (
    echo.
    echo [LOI] Vui long bam chuot phai file nay va chon Run as administrator.
) else (
    echo.
    echo [OK] Da sua loi logo qua IP LAN.
)
pause
