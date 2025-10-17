@echo off
echo ========================================
echo  Drone Delivery System - Feature Testing
echo  Date: October 16, 2025
echo ========================================
echo.

:menu
echo.
echo Select Test to Run:
echo.
echo 1. Run Migrations (Setup)
echo 2. Test Auto-Assignment Command
echo 3. Test with Emergency Alert Check
echo 4. View Queue Status
echo 5. Start Laravel Scheduler (Auto-assign every 5 min)
echo 6. View Logs (Real-time)
echo 7. Test Tinker (OTP Methods)
echo 8. Clear Cache
echo 9. Exit
echo.
set /p choice="Enter your choice (1-9): "

if "%choice%"=="1" goto migrations
if "%choice%"=="2" goto autoassign
if "%choice%"=="3" goto alerts
if "%choice%"=="4" goto status
if "%choice%"=="5" goto scheduler
if "%choice%"=="6" goto logs
if "%choice%"=="7" goto tinker
if "%choice%"=="8" goto cache
if "%choice%"=="9" goto end
goto menu

:migrations
echo.
echo Running migrations...
echo.
php artisan migrate
pause
goto menu

:autoassign
echo.
echo Running auto-assignment command...
echo.
php artisan deliveries:auto-assign
echo.
pause
goto menu

:alerts
echo.
echo Checking emergency alerts...
echo.
php artisan deliveries:auto-assign --check-alerts
echo.
pause
goto menu

:status
echo.
echo Queue Status:
echo.
php artisan tinker --execute="echo json_encode(\App\Services\DeliveryPriorityQueue::getQueueStatus(), JSON_PRETTY_PRINT);"
echo.
pause
goto menu

:scheduler
echo.
echo Starting Laravel Scheduler...
echo This will run auto-assignment every 5 minutes
echo Press Ctrl+C to stop
echo.
php artisan schedule:work
pause
goto menu

:logs
echo.
echo Viewing logs in real-time...
echo Press Ctrl+C to stop
echo.
powershell -Command "Get-Content storage/logs/laravel.log -Wait -Tail 50"
pause
goto menu

:tinker
echo.
echo Opening Tinker to test OTP methods...
echo.
echo Try these commands:
echo   $delivery = App\Models\Delivery::first();
echo   $otp = $delivery-^>generateOTP();
echo   $delivery-^>verifyOTP($otp, 'Test User');
echo   $delivery-^>getOTPStatus();
echo.
php artisan tinker
goto menu

:cache
echo.
echo Clearing cache...
echo.
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo.
echo Cache cleared!
pause
goto menu

:end
echo.
echo Goodbye!
exit
