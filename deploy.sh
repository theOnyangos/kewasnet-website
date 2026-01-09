commit 52149820ff1facb59607fc2284c584f00efc2932
Author: Dennis Otieno <denonyango@gmail.com>
Date:   Sat Jan 10 00:42:01 2026 +0300

    Added more permissions to the deploy script

diff --git a/deploy.sh b/deploy.sh
index 60fbef9..5b6d64e 100755
--- a/deploy.sh
+++ b/deploy.sh
@@ -300,11 +300,45 @@ cd "$DEPLOY_PATH" || {
     exit 1
 }
 
+# Handle Git safe.directory and ownership issues
+# Git 2.35.1+ requires repositories to be owned by the current user or listed in safe.directory
+CURRENT_USER=$(whoami)
+
+# First, ensure .git directory is owned by the deploy user for git operations
+if [ -d ".git" ] && [ "$CURRENT_USER" != "root" ]; then
+    REPO_OWNER=$(stat -c '%U' ".git" 2>/dev/null || stat -f '%Su' ".git" 2>/dev/null || echo "")
+    if [ -n "$REPO_OWNER" ] && [ "$REPO_OWNER" != "$CURRENT_USER" ]; then
+        print_info "Adjusting .git ownership from $REPO_OWNER to $CURRENT_USER for git operations..."
+        sudo chown -R $CURRENT_USER:$CURRENT_USER ".git" 2>/dev/null || \
+        print_warning "Could not change .git ownership. Will try using safe.directory instead."
+    fi
+fi
+
+# Configure Git safe.directory as backup (in case ownership fix didn't work)
+if [ "$CURRENT_USER" != "root" ] && [ -n "$CURRENT_USER" ]; then
+    print_info "Configuring Git safe.directory for $CURRENT_USER..."
+    # Ensure we use the correct user's home directory for git config
+    export HOME=$(eval echo ~$CURRENT_USER 2>/dev/null || echo "$HOME")
+    
+    # Add specific directory to safe.directory list
+    git config --global --add safe.directory "$DEPLOY_PATH" 2>&1 | grep -v "already exists" > /dev/null || true
+    
+    # Also add wildcard pattern as fallback (for any nested git repos)
+    git config --global --add safe.directory '*' 2>&1 | grep -v "already exists" > /dev/null || true
+    
+    print_success "Git safe.directory configured"
+fi
+
 # Initialize git if not already initialized
 if [ ! -d ".git" ]; then
     print_info "Initializing git repository..."
     git init
-    git remote add origin https://github.com/theOnyangos/$APP_NAME.git
+    git remote add origin https://@github.com/theOnyangos/$APP_NAME.git 2>/dev/null || \
+    (git remote remove origin 2>/dev/null; git remote add origin https://@github.com/theOnyangos/$APP_NAME.git)
+    # Ensure .git is owned by deploy user after initialization
+    if [ "$CURRENT_USER" != "root" ]; then
+        sudo chown -R $CURRENT_USER:$CURRENT_USER ".git" 2>/dev/null || true
+    fi
 fi
 
 # Fetch and pull latest changes
@@ -400,12 +434,30 @@ sudo chown -R $WEB_USER:$WEB_GROUP "$DEPLOY_PATH/public/uploads" 2>/dev/null ||
 chown -R $WEB_USER:$WEB_GROUP "$DEPLOY_PATH/public/uploads" 2>/dev/null || \
 print_warning "Could not set ownership on uploads directory"
 
-# Set ownership on entire deployment directory
+# Set ownership on entire deployment directory (but preserve .git ownership for deploy user)
 print_info "Setting ownership on deployment directory to $WEB_USER:$WEB_GROUP..."
+# Get current user running the script
+CURRENT_USER=$(whoami)
+# Save .git ownership before changing everything
+GIT_OWNER=""
+if [ -d "$DEPLOY_PATH/.git" ]; then
+    # Get current owner of .git directory (works on both Linux and macOS)
+    GIT_OWNER=$(stat -c '%U' "$DEPLOY_PATH/.git" 2>/dev/null || stat -f '%Su' "$DEPLOY_PATH/.git" 2>/dev/null || echo "")
+fi
+
+# Set ownership on entire directory
 sudo chown -R $WEB_USER:$WEB_GROUP "$DEPLOY_PATH" 2>/dev/null || \
 chown -R $WEB_USER:$WEB_GROUP "$DEPLOY_PATH" 2>/dev/null || \
 print_warning "Could not set ownership. You may need to run: sudo chown -R $WEB_USER:$WEB_GROUP $DEPLOY_PATH"
 
+# If .git exists and deploy user is different from web user, restore .git ownership to deploy user
+# This ensures git operations can run without safe.directory issues
+if [ -d "$DEPLOY_PATH/.git" ] && [ "$CURRENT_USER" != "root" ] && [ "$CURRENT_USER" != "$WEB_USER" ] && [ -n "$CURRENT_USER" ]; then
+    print_info "Preserving .git ownership for deploy user ($CURRENT_USER) to enable git operations..."
+    sudo chown -R $CURRENT_USER:$CURRENT_USER "$DEPLOY_PATH/.git" 2>/dev/null || \
+    print_warning "Could not set .git ownership. Git operations may fail. Run: sudo chown -R $CURRENT_USER:$CURRENT_USER $DEPLOY_PATH/.git"
+fi
+
 # Set general directory permissions (we'll fix writable separately after)
 print_info "Setting general directory permissions..."
 # Use a simpler approach - set permissions on common directories first
