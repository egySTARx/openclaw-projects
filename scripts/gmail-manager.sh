#!/bin/bash
# Quick script to check for new Gmail messages

cd "$(dirname "$0")"

echo "🔍 Checking for new Gmail messages..."

# Run the Node.js script
node gmail-manager.js check

echo ""
echo "📝 For more info, see README-gmail.md"
