@echo on
cls
set real_path=%~DP0
:start
php apigr.php sonclod --filter=yes
pause
