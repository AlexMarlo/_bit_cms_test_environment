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

echo -e ${c_h_red}REVERT ${c_std}
svn revert settings/admin_navigation_item.conf.php settings/admin_user_group.conf.php src/model
echo -e ${c_h_red}DELETE ${c_std}
sudo rm -rf src/model src/controller src/finder template/test template/admin_test template/user_test
php cli/constructor.php create test
svn status
