@echo OFF
cls
set real_path=%~DP0
:start
set /p project=LP blog :
php apigr.php pixnet --blog=%project%
pause
