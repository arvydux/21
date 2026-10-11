#!/bin/bash
# Rebuilds menu-7-print.html, menu-7-mobile.html and menu-7.pdf from data.py / pl.py.
set -euo pipefail
cd "$(dirname "$0")"
OUT=..
PYTHONDONTWRITEBYTECODE=1 python3 build.py "$OUT"
"/Applications/Google Chrome.app/Contents/MacOS/Google Chrome" --headless=new --disable-gpu \
  --no-pdf-header-footer --virtual-time-budget=10000 \
  --print-to-pdf="$OUT/menu-7.pdf" "file://$(cd "$OUT" && pwd)/menu-7-print.html" 2>/dev/null
echo "menu-7.pdf updated"
