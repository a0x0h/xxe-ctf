# Deployment script for XXE CTF Challenge

# Server configuration
SERVER_IP="91.228.186.44"
DOMAIN="abphz.tech"
WEB_ROOT="/var/www/html"

# Deploy files to server
echo "Deploying XXE CTF Challenge to $DOMAIN ($SERVER_IP)..."

# Create necessary directories
ssh root@$SERVER_IP "mkdir -p $WEB_ROOT/uploads $WEB_ROOT/logs"

# Copy files to server
rsync -avz --exclude='.git' --exclude='deploy.sh' . root@$SERVER_IP:$WEB_ROOT/

# Set proper permissions
ssh root@$SERVER_IP "
    chown -R www-data:www-data $WEB_ROOT
    chmod 755 $WEB_ROOT/public
    chmod 777 $WEB_ROOT/uploads
    chmod 777 $WEB_ROOT/logs
"

echo "Deployment completed!"
echo "Access the CTF at: http://$DOMAIN"

# Optional: Start xxeserv for payload capture
echo "To capture XXE payloads, run:"
echo "xxeserv -o files.log -p 2121 -w -wd public -wp 8000"
