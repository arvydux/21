@echo off
rem Starts the POS on http://127.0.0.1:8000 and restarts it whenever it stops.
rem Start with Windows: Win+R -> shell:startup -> put a shortcut to this file there.
title Kasa - Laravel serveris
cd /d "%~dp0"

set "PHP=php"
where php >nul 2>nul || if exist "C:\xampp\php\php.exe" set "PHP=C:\xampp\php\php.exe"

if exist "public\hot" del "public\hot"

:serve
"%PHP%" artisan serve --host=127.0.0.1 --port=8000
echo.
echo Serveris sustojo. Paleidziama is naujo po 5 sekundziu...
timeout /t 5 /nobreak >nul
goto serve
