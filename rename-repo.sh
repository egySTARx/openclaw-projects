#!/bin/bash
# Rename repository to meemradius

curl -X PUT \
  -H "Authorization: token ghp_ytbsNy97zJ34PGi3w8vdofkpvxgJx2SuQMV" \
  -H "Accept: application/vnd.github.v3+json" \
  https://api.github.com/repos/egySTARx/openclaw-projects \
  -d '{"name":"meemradius","description":"Mageek's Complete RADIUS System - MeemRadius"}'
