#!/bin/bash

"/Applications/Google Chrome.app/Contents/MacOS/Google Chrome" \
  --headless \
  --disable-gpu \
  --print-to-pdf="public/menu-63-merged.pdf" \
  --no-pdf-header-footer \
  --print-to-pdf-no-header \
  "file://$(pwd)/menus/menu-6-merged.html" 2>/dev/null

cp public/menu-63-merged.pdf menu-63-merged.pdf

echo "PDF generated: public/menu-63-merged.pdf + menu-63-merged.pdf"
