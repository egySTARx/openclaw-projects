#!/bin/bash
# Quick script to check for meetings

cd "$(dirname "$0")"

echo "📅 Checking for meetings..."

# Run the Node.js script
node meeting-manager.js check

echo ""
echo "📝 For more info, see README-meetings.md"
