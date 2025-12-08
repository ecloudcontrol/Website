#!/bin/bash
# Remove the Hello Dolly plugin using WP-CLI
if wp --allow-root plugin is-installed hello; then
    wp --allow-root plugin delete hello
    echo "Hello Dolly plugin removed."
else
    echo "Hello Dolly plugin not found."
fi
filename="mychildtheme_add_google_analytics"

header_file="/var/www/html/wp-content/themes/Avada-Child-Theme/functions.php"
injection_file="/wp-contents/plugin-customization/avada_childtheme_add_google_analytics_functions_append.php"

if grep -q "$filename" "$header_file"; then
    echo "The Google Analytics snippet already exists in functions.php"
else
    awk -v file="$injection_file" '/add_action\( '\''after_setup_theme'\'', '\''avada_lang_setup'\'' \);/ {print; while ((getline line < file) > 0) print line; next}1' "$header_file" > "${header_file}.tmp" && mv "${header_file}.tmp" "$header_file"

    if [ $? -eq 0 ]; then
        echo "functions.php successfully updated"
    else
        echo "Failed to update functions.php"
    fi
fi
# Define the header.php file path
HEADER_FILE="/var/www/html/wp-content/themes/Avada/header.php"

# Define the content to insert into <head>
HEAD_CONTENT=$(cat << 'EOF'
    <title>Chat Widget Example</title>
    <!-- Add React and ReactDOM -->
    <script crossorigin src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
    <script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
    <!-- Add the chat widget and its styles -->
    <link rel="stylesheet" href="https://sitechime2025b.lowtouch.ai/data/openai-chat-widget.css">
    <script src="https://sitechime2025b.lowtouch.ai/data/chat-widget.umd.js"></script>
EOF
)

# Define the content to insert into <body>
BODY_CONTENT=$(cat << 'EOF'
    <div id="chat-widget"></div>
    <script>
        // Initialize the chat widget
        const { mountChatWidget } = SiteChimeWidget;
        mountChatWidget('chat-widget', {
            apiKey: '',
            configUrl: 'https://sitechime2025b.lowtouch.ai/data/widget-config.json',
            position: 'bottom-right',
            theme: {
                primary: '#007bff',
                secondary: '#ffffff',
                text: '#000000',
                surface: '#ffffff',
                border: '#e5e7eb'
            }
        });
    </script>
EOF
)

# Escape special characters for sed
HEAD_CONTENT=$(echo "$HEAD_CONTENT" | sed -e 's/[\/&]/\\&/g' -e 's/$/\\n/' | tr -d '\n')
BODY_CONTENT=$(echo "$BODY_CONTENT" | sed -e 's/[\/&]/\\&/g' -e 's/$/\\n/' | tr -d '\n')

# Backup the original header.php
cp "$HEADER_FILE" "${HEADER_FILE}.bak"

# Insert head content before </head>
sed -i "/<\/head>/i $HEAD_CONTENT" "$HEADER_FILE"

# Insert body content after <div id="wrapper">
sed -i "/<div id=\"wrapper\"/a $BODY_CONTENT" "$HEADER_FILE"

echo "header.php has been updated with the chat widget code."
