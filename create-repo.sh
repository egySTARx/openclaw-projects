#!/bin/bash

# Configuration
REPO="meemradius"
DESCRIPTION="Mageek's Complete RADIUS System - MeemRadius"

# Create repository
curl -X POST \
  -H "Authorization: token ghp_ytbsNy97zJ34PGi3w8vdofkpvxgJx2SuQMV" \
  -H "Accept: application/vnd.github.v3+json" \
  -d "{\"name\":\"$REPO\",\"description\":\"$DESCRIPTION\",\"private\":false,\"auto_init\":false,\"has_wiki\":false,\"has_pages\":false}" \
  https://api.github.com/user/repos

echo ""
echo "Repository: $REPO created!"
