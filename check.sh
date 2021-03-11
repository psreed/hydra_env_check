#!/bin/sh
clear
git pull
echo "#################################"
echo ""
echo ""
/usr/bin/php -d display_errors=on ./check.php
