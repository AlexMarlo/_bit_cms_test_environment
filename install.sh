#!/bin/bash
# Переменные с именами цветов
c_std="\E[0;39m"
c_h_std="\E[1;37m"
c_pink="\E[0;35m"
c_h_pink="\E[1;35m"
c_red="\E[0;31m"
c_h_red="\E[1;31m"
c_cayan="\E[0;36m"
c_h_cayan="\E[1;36m"
c_yellow="\E[1;33m"
c_green="\E[0;32m"
c_h_green="\E[1;32m"
c_blue="\E[0;34m"
c_h_blue="\E[1;34m"

#~ echo -e ${c_h_cayan}Это непонятный \:\) цвет${c_std}
#~ echo -e ${c_h_red}Это красный цвет${c_std}
#~ echo -e ${c_yellow}Это жёлтый цвет${c_std}
#~ echo -e ${c_green}Это зелёный цвет${c_std}
#~ echo -e ${c_blue}Это голубой цвет${c_std}

work_directory="./"

#~ ********************************************************
sudo rm -rf ${work_directory}project ${work_directory}git
mkdir ${work_directory}project ${work_directory}git
cp -r ${work_directory}_install ${work_directory}project/
#~ ********************************************************

#~ ********************************************************
notify-send "START install test bit cms project"
echo -e ${c_h_green}prepare install done.${c_std}
#~ ********************************************************

cd ${work_directory}git
git init --bare
touch README
git add .
git commit -m " -- init commit"
cd ..

echo -e ${c_h_green}prepare git done.${c_std}
echo ""
echo -e ${c_h_red}START INSTALL${c_std}
echo ""

php ${work_directory}project/_install/install.php

echo ""
echo -e ${c_h_red}POST INSTALL preparing${c_std}
echo ""

cp ${work_directory}mail.conf.override.php ${work_directory}project/settings/

chmod 777 ${work_directory}project/var
mkdir ${work_directory}project/var/logs
cp ${work_directory}error.log ${work_directory}project/var/logs/
chmod 777 ${work_directory}project/var/logs/error.log

chmod 777 ${work_directory}project/www/media

#~ ********************************************************
echo -e ${c_h_green}post install preparing done.${c_std}
echo -e ${c_h_green}INSTALL complete${c_std}
notify-send "END install test bit cms project"
#~ ********************************************************
