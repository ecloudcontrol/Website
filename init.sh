#!/bin/bash
cd /var/www/html || exit 1

echo "Checking for Hello Dolly plugin..."
if wp --allow-root plugin is-installed hello; then
    echo "Hello Dolly plugin found. Deleting..."
    wp --allow-root plugin delete hello || echo "⚠️ WP-CLI delete command failed"
    echo "✅ Hello Dolly plugin removed."
else
    echo "❌ Hello Dolly plugin not found."
fi

filename="AppZ-Healthcare-Case-Study-Oct-2020-v2-1"
if  grep $filename  -q  /var/www/html/wp-content/themes/Avada/header.php ;then
  echo "theme change already exist..."
else
    sed -n -i -e '/<?php wp_head(); ?>/r /wp-contents/plugin-customization/contact7_download.php' -e 1x -e '2,${x;p}' -e '${x;p}' /var/www/html/wp-content/themes/Avada/header.php 2>error.txt
    if [ -s error.txt ];then
	    rm error.txt
	    echo "failed to update header.php"
    else
        echo "header.php successfully updated"
fi
rm error.txt
fi
filename1="CloudControl-Approach-Database-Migration-and-Management-V1"
if  grep $filename1  -q  /var/www/html/wp-content/themes/Avada/header.php ;then
  echo "theme change already exist..."
else
    sed -n -i -e '/<?php wp_head(); ?>/r /wp-contents/plugin-customization/contact7_download_01.php' -e 1x -e '2,${x;p}' -e '${x;p}' /var/www/html/wp-content/themes/Avada/header.php 2>error.txt
    if [ -s error.txt ];then
            rm error.txt
            echo "failed to update header.php"
    else
        echo "header.php successfully updated"
fi
rm error.txt
fi
filename2="CC_Brochure_May2021-1"
if  grep $filename2  -q  /var/www/html/wp-content/themes/Avada/header.php ;then
  echo "theme change already exist..."
else
    sed -n -i -e '/<?php wp_head(); ?>/r /wp-contents/plugin-customization/contact7_download_02.php' -e 1x -e '2,${x;p}' -e '${x;p}' /var/www/html/wp-content/themes/Avada/header.php 2>error.txt
    if [ -s error.txt ];then
            rm error.txt
            echo "failed to update header.php"
    else
        echo "header.php successfully updated"
fi
rm error.txt
fi

filename3="AppZ-CloudControl-brochure-Oct-21"
if  grep $filename3  -q  /var/www/html/wp-content/themes/Avada/header.php ;then
  echo "theme change already exist..."
else
    sed -n -i -e '/<?php wp_head(); ?>/r /wp-contents/plugin-customization/contact7_download_03.php' -e 1x -e '2,${x;p}' -e '${x;p}' /var/www/html/wp-content/themes/Avada/header.php 2>error.txt
    if [ -s error.txt ];then
            rm error.txt
            echo "failed to update header.php"
    else
        echo "header.php successfully updated"
fi
rm error.txt
fi

filename4="Case-Study-EdTech-Cloud-Native-v05"
if  grep $filename4  -q  /var/www/html/wp-content/themes/Avada/header.php ;then
  echo "theme change already exist..."
else
    sed -n -i -e '/<?php wp_head(); ?>/r /wp-contents/plugin-customization/contact7_download_04.php' -e 1x -e '2,${x;p}' -e '${x;p}' /var/www/html/wp-content/themes/Avada/header.php 2>error.txt
    if [ -s error.txt ];then
            rm error.txt
            echo "failed to update header.php"
    else
        echo "header.php successfully updated"
fi
rm error.txt
fi

filename5="CCS-AppZ-for-AutoMobile-Company-V01"
if  grep $filename5  -q  /var/www/html/wp-content/themes/Avada/header.php ;then
  echo "theme change already exist..."
else
    sed -n -i -e '/<?php wp_head(); ?>/r /wp-contents/plugin-customization/contact7_download_05.php' -e 1x -e '2,${x;p}' -e '${x;p}' /var/www/html/wp-content/themes/Avada/header.php 2>error.txt
    if [ -s error.txt ];then
            rm error.txt
            echo "failed to update header.php"
    else
        echo "header.php successfully updated"
fi
rm error.txt
fi

filename6="Signature-SRE-Dec2022-v8"
if  grep $filename6  -q  /var/www/html/wp-content/themes/Avada/header.php ;then
  echo "theme change already exist..."
else
    sed -n -i -e '/<?php wp_head(); ?>/r /wp-contents/plugin-customization/contact7_download_06.php' -e 1x -e '2,${x;p}' -e '${x;p}' /var/www/html/wp-content/themes/Avada/header.php 2>error.txt
    if [ -s error.txt ];then
            rm error.txt
            echo "failed to update header.php"
    else
        echo "header.php successfully updated"
fi
rm error.txt
fi
filename7="White-Paper_-Migrating-Data-from-Sybase-to-Azure-SQL-Server-v7"
if  grep $filename6  -q  /var/www/html/wp-content/themes/Avada/header.php ;then
  echo "theme change already exist..."
else
    sed -n -i -e '/<?php wp_head(); ?>/r /wp-contents/plugin-customization/contact7_download_07.php' -e 1x -e '2,${x;p}' -e '${x;p}' /var/www/html/wp-content/themes/Avada/header.php 2>error.txt
    if [ -s error.txt ];then
            rm error.txt
            echo "failed to update header.php"
    else
        echo "header.php successfully updated"
fi
rm error.txt
fi
