#!/bin/bash
# Local Deployment script for XXE CTF Challenge

# Server configuration
DOMAIN="abphz.tech"
WEB_ROOT="/var/www/html"

echo "Deploying XXE CTF Challenge locally..."

# Stop any running services that might conflict
echo "Stopping conflicting services..."
pm2 stop all 2>/dev/null || true

# Create necessary directories
echo "Creating directories..."
mkdir -p $WEB_ROOT/uploads $WEB_ROOT/logs

# Backup existing content
echo "Backing up existing content..."
mkdir -p /home/backup/$(date +%Y%m%d_%H%M%S)
cp -r $WEB_ROOT/* /home/backup/$(date +%Y%m%d_%H%M%S)/ 2>/dev/null || true

# Clear web root and copy new files
echo "Deploying XXE CTF files..."
rm -rf $WEB_ROOT/*
cp -r public/* $WEB_ROOT/
cp -r includes $WEB_ROOT/
cp flag.txt $WEB_ROOT/
cp -r example-payloads $WEB_ROOT/

# Set proper permissions
echo "Setting permissions..."
chown -R www-data:www-data $WEB_ROOT
chmod 755 $WEB_ROOT/
chmod 644 $WEB_ROOT/*.php 2>/dev/null || true
chmod 644 $WEB_ROOT/includes/*.php
chmod 777 $WEB_ROOT/uploads
chmod 777 $WEB_ROOT/logs

# Install required PHP extensions
echo "Installing PHP extensions..."
apt update -y
apt install -y php-fpm php-xml php-zip php-dom php-mbstring nginx

# Configure Nginx for XXE CTF
echo "Configuring Nginx..."
cat > /etc/nginx/sites-available/xxe-ctf << 'EOF'
server {
    listen 80;
    server_name abphz.tech 91.228.186.44;
    root /var/www/html;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    access_log /var/log/nginx/xxe-ctf_access.log;
    error_log /var/log/nginx/xxe-ctf_error.log;
}
EOF

# Enable the site
echo "Enabling XXE CTF site..."
rm -f /etc/nginx/sites-enabled/*
ln -s /etc/nginx/sites-available/xxe-ctf /etc/nginx/sites-enabled/

# Test and restart services
echo "Starting services..."
nginx -t
systemctl restart nginx
systemctl restart php8.1-fpm
systemctl enable nginx
systemctl enable php8.1-fpm

echo ""
echo "🎯 Deployment completed successfully!"
echo "🌐 Access the XXE CTF at: http://$DOMAIN"
echo "📁 Files deployed to: $WEB_ROOT"
echo ""
echo "🔍 To test locally:"
echo "curl -I http://localhost"
echo "curl -I http://$DOMAIN"
echo ""
echo "📡 To capture XXE payloads, run:"
echo "xxeserv -o files.log -p 2121 -w -wd $WEB_ROOT -wp 8000"
