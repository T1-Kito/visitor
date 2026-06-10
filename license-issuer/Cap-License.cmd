@echo off
setlocal
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0Cap-License.ps1"
if errorlevel 1 (
    echo.
    echo [LOI] Khong cap duoc license. Vui long kiem tra thong bao phia tren.
)
pause
