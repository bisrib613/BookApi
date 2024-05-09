@echo OFF
cls
set real_path=%~DP0
npm i mel-spintax csvtojson gtts cheerio
npm audit fix
pause