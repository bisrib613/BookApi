@echo ON
cls
set real_path=%~DP0
:start
php apigr.php pdf
pause
