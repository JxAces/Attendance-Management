@echo off
for /f "tokens=2 delims=:" %%A in ('ipconfig ^| findstr /c:"IPv4 Address"') do (
    set "IP_ADDRESS=%%A"
)
set "IP_ADDRESS=%IP_ADDRESS:~1%"
cd /d "C:\xampp\htdocs\Attendance-Management"
start "" http://%IP_ADDRESS%:8000
php artisan serve --host=%IP_ADDRESS%
