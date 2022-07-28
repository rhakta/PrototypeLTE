@echo off
title Git Pull
echo Debut de l'execution des commandes.
cd ..
git clone https://github.com/rhakta/PrototypeLTE.git
git add *
git commit -am "Commit at the moment"
git pull https://github.com/rhakta/PrototypeLTE.git
echo Fin des commandes.
pause