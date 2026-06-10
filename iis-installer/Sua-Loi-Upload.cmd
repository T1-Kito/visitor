@echo off
chcp 65001 >nul
title Sua loi upload logo - Khach Moi VMS
set "FIX_SCRIPT=%~dp0tools\Fix-Upload-Temp.ps1"
if not exist "%FIX_SCRIPT%" set "FIX_SCRIPT=%~dp0Fix-Upload-Temp.ps1"
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%FIX_SCRIPT%"
if errorlevel 1 (
    echo.
    echo [LOI] Vui long bam chuot phai file nay va chon Run as administrator.
) else (
    echo.
    echo [OK] Da sua loi upload logo.
)
pause
