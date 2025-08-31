# Alexandria HTTP/3 Migration - Deployment Guide

## Overview
This migration replaces Apache with Nginx + PHP-FPM to enable HTTP/3 (QUIC) support for local development.

## Changes Made
- **Dockerfile**: Changed from `php:8.4-apache` to `php:8.4-fpm`
- **Web Server**: Nginx with HTTP/3 (QUIC) and HTTP/2 support
- **Working Directory**: Updated from `/var/www` to `/var/www/html`
- **Ports**: HTTP on 8080, HTTPS on 8443 (TCP + UDP for QUIC)
- **SSL**: Self-signed certificates generated automatically

## Quick Start
1. Ensure Docker Desktop is running
2. Configure database (copy `includes/default.db.auth.php` to `includes/db.auth.php`)
3. Start services: `docker-compose up -d --build`
4. Access the application:
   - HTTP: http://localhost:8080 (redirects to HTTPS)
   - HTTPS: https://localhost:8443
   - Test page: https://localhost:8443/http3-test.php

## Testing HTTP/3
### Browser Testing
1. Open browser developer tools (F12)
2. Go to Network tab
3. Load any page
4. Look for "Protocol" column showing "h3" or "http/3"

### Command Line Testing
```bash
# HTTP/3 (requires curl with HTTP/3 support)
curl --http3 -k https://localhost:8443/

# HTTP/2 (fallback)
curl --http2 -k https://localhost:8443/

# Check Alt-Svc header for HTTP/3 availability
curl -k -I https://localhost:8443/ | grep -i alt-svc
```

## Architecture
```
[Browser] --HTTP/3 (QUIC)--> [Nginx:8443] --FastCGI--> [PHP-FPM:9000]
[Browser] --HTTP/2-------> [Nginx:8443] --FastCGI--> [PHP-FPM:9000]
                           [Nginx] ----Static Files----> [/var/www/html]
```

## File Structure
```
alexandria/
├── nginx/
│   ├── nginx.conf              # Main Nginx configuration
│   ├── conf.d/default.conf     # Site-specific configuration
│   ├── certs/                  # Auto-generated SSL certificates
│   └── generate-cert.sh        # Manual certificate generation script
├── Dockerfile                  # PHP-FPM container
├── docker-compose.yml          # Multi-container setup
└── www/
    ├── http3-test.php          # HTTP/3 test page
    └── ... (existing files)
```

## Troubleshooting
### Container Issues
```bash
# Check container status
docker-compose ps

# View logs
docker-compose logs nginx
docker-compose logs php

# Restart services
docker-compose restart
```

### SSL Certificate Issues
```bash
# Regenerate certificates manually
cd nginx
./generate-cert.sh

# Or restart nginx container to auto-generate
docker-compose restart nginx
```

### Port Conflicts
If ports 8080 or 8443 are in use, update docker-compose.yml:
```yaml
ports:
  - "9080:80"   # Change to available port
  - "9443:443"  # Change to available port
  - "9443:443/udp"
```

## Benefits
- **HTTP/3 (QUIC)**: Faster connection establishment and improved performance
- **Modern Protocol Support**: TLS 1.3, HTTP/2, HTTP/3
- **Better Performance**: Nginx serves static files directly
- **Future-Ready**: Latest web technologies for local development
- **Compatibility**: Maintains all existing Alexandria functionality

## Rollback
To revert to Apache setup:
```bash
git checkout main -- Dockerfile docker-compose.yml tools/entrypoint.sh
docker-compose down
docker-compose up -d --build
```