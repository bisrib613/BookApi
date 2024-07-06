@echo OFF
cls
set real_path=%~DP0
set /p project=LP blog :
:start
php apigr.php feed --blog=%project%
pause
