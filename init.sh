#!/bin/bash

filename="mychildtheme_add_google_analytics"

header_file="/var/www/html/wp-content/themes/Avada-Child-Theme/functions.php"
injection_file="/var/www/html/wp-content/plugin-customization/avada_childtheme_add_google_analytics_functions_append.php"

if grep -q "$filename" "$header_file"; then
    echo "The Google Analytics snippet already exists in functions.php"
else
    sed -i "/add_action( 'after_setup_theme', 'avada_lang_setup' );/r $injection_file" "$header_file"

    if [ $? -eq 0 ]; then
        echo "functions.php successfully updated"
    else
        echo "Failed to update functions.php"
    fi
fi
