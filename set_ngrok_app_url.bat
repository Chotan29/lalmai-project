@echo off
setlocal enabledelayedexpansion

cd /d "%~dp0"

for /f "usebackq delims=" %%i in (`powershell -NoProfile -Command "try { $t=(Invoke-RestMethod 'http://127.0.0.1:4040/api/tunnels').tunnels ^| Where-Object { $_.public_url -like 'https://*' } ^| Select-Object -First 1 -ExpandProperty public_url; if ($t) { $t } } catch { '' }"`) do set NGROK_URL=%%i

if "%NGROK_URL%"=="" (
  echo [ERROR] Could not detect ngrok HTTPS URL.
  echo         Make sure ngrok is running: ngrok http 8000
  exit /b 1
)

powershell -NoProfile -Command "$p='.env'; if (!(Test-Path $p)) { Write-Error '.env not found'; exit 1 }; $c=Get-Content $p -Raw; if ($c -match '(?m)^APP_URL=') { $c=[regex]::Replace($c,'(?m)^APP_URL=.*','APP_URL=%NGROK_URL%') } else { $c=$c + [Environment]::NewLine + 'APP_URL=%NGROK_URL%' }; Set-Content -Path $p -Value $c -NoNewline"
if errorlevel 1 exit /b 1

C:\xampp\php\php.exe artisan config:clear >nul
C:\xampp\php\php.exe artisan cache:clear >nul
C:\xampp\php\php.exe artisan route:clear >nul

echo [OK] APP_URL updated to: %NGROK_URL%
echo [OK] Laravel config/cache/route cleared.
echo [NEXT] Start payment flow from: %NGROK_URL%/online-registration
endlocal
