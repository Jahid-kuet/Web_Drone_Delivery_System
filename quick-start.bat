@echo off
REM ========================================
REM Quick Start Script for Testing
REM ========================================

echo.
echo ======================================
echo   Drone Delivery System - Quick Start
echo ======================================
echo.

echo [Step 1/4] Clearing caches...
call php artisan optimize:clear
echo.

echo [Step 2/4] Checking database migrations...
call php artisan migrate:status
echo.

echo [Step 3/4] Starting development server...
echo.
echo Server will start at: http://127.0.0.1:8000
echo.
echo ======================================
echo   TEST LOGIN CREDENTIALS
echo ======================================
echo.
echo ADMIN:
echo   Email: admin@drone.com
echo   Password: password123
echo   URL: http://127.0.0.1:8000/login
echo.
echo HOSPITAL ADMIN:
echo   Email: hospital@drone.com
echo   Password: password123
echo.
echo DRONE OPERATOR:
echo   Email: operator@drone.com
echo   Password: password123
echo.
echo ======================================
echo   Press Ctrl+C to stop the server
echo ======================================
echo.

call php artisan serve
