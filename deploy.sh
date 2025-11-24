#!/bin/bash

echo "🚀 Deploying Herba Scan Dashboard with Nginx Proxy Manager support"

# Create production environment file
echo "📝 Setting up production environment..."
cp .env.docker .env

# Update APP_URL to your actual domain
echo "⚠️  IMPORTANT: Update APP_URL in .env to your actual domain (e.g., https://yourdomain.com)"
echo "   Current APP_URL: $(grep APP_URL .env)"

# Build and deploy containers
echo "🔨 Building containers..."
docker-compose down --volumes
docker-compose build --no-cache
docker-compose up -d

echo "⏳ Waiting for services to start..."
sleep 30

# Check if containers are running
echo "📊 Checking container status..."
docker-compose ps

# Display important information
echo ""
echo "✅ Deployment completed!"
echo ""
echo "📋 Next steps for Nginx Proxy Manager:"
echo "1. Create a new Proxy Host in Nginx Proxy Manager"
echo "2. Set the following configuration:"
echo "   - Domain Names: your-domain.com"
echo "   - Scheme: http"
echo "   - Forward Hostname/IP: [your-docker-host-ip]"
echo "   - Forward Port: 8000 (if using default port mapping)"
echo "   - Enable 'Cache Assets', 'Block Common Exploits', 'Websockets Support'"
echo "3. In the SSL tab, request a Let's Encrypt certificate"
echo "4. In the Advanced tab, add this custom location:"
echo ""
echo "   Custom Nginx Configuration:"
echo "   proxy_set_header X-Forwarded-Proto https;"
echo "   proxy_set_header X-Forwarded-Host \$host;"
echo "   proxy_set_header X-Forwarded-Port \$server_port;"
echo "   client_max_body_size 100M;"
echo ""
echo "🔗 Application will be available at: https://your-domain.com"
echo "🔗 Admin panel: https://your-domain.com/admin"
echo "🔗 API: https://your-domain.com/api/v1/"
echo ""
echo "⚠️  Remember to update APP_URL in .env with your actual domain!"