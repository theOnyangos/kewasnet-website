#!/bin/bash
# Remove the commit with secrets from history
echo "Removing sensitive data from git history..."
git filter-branch --force --index-filter \
  "git rm --cached --ignore-unmatch app/Database/Seeds/GoogleSettingsSeeder.php" \
  --prune-empty --tag-name-filter cat -- --all

# Add the updated file back
git add app/Database/Seeds/GoogleSettingsSeeder.php
git commit -m "fix: Use environment variables for Google OAuth credentials instead of hardcoded values"

echo "Done! Now force push: git push origin main --force"
