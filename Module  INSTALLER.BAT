@echo OFF
cls
set real_path=%~DP0
npm i mel-spintax csvtojson gtts cheerio @google/generative-ai axios 
npm audit fix
pause
