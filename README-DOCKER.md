# Herba Scan Dashboard - Docker Setup

Laravel application dengan Filament admin panel yang dikonfigurasi untuk deployment menggunakan Docker dan Nginx Proxy Manager.

## 🚀 Quick Start

### 1. Development (Local)
```bash
# Clone repository
git clone <your-repo-url>
cd herba-scan-dashboard

# Start development environment
docker-compose up --build -d

# Akses aplikasi
# Web: http://localhost:8000
# Admin: http://localhost:8000/admin
# API: http://localhost:8000/api/v1/
# PHPMyAdmin: http://localhost:8080
```

### 2. Production dengan Nginx Proxy Manager

#### Persiapan Server
1. Pastikan Docker dan Docker Compose sudah terinstall
2. Pastikan Nginx Proxy Manager sudah berjalan di server

#### Deploy Aplikasi
```bash
# Update domain di file .env.docker
nano .env.docker
# Ganti APP_URL=https://your-domain.com dengan domain Anda

# Jalankan script deployment
./deploy.sh
```

#### Konfigurasi Nginx Proxy Manager

1. **Buat Proxy Host baru:**
   - Domain Names: `your-domain.com`
   - Scheme: `http`
   - Forward Hostname/IP: `IP_SERVER_DOCKER`
   - Forward Port: `8000` (atau port yang Anda mapping)

2. **SSL Tab:**
   - Request SSL Certificate (Let's Encrypt)
   - Force SSL: ON
   - HTTP/2 Support: ON

3. **Advanced Tab - Custom Nginx Configuration:**
   ```nginx
   # Proxy headers untuk Laravel
   proxy_set_header X-Forwarded-Proto $scheme;
   proxy_set_header X-Forwarded-Host $host;
   proxy_set_header X-Forwarded-Port $server_port;
   proxy_set_header X-Real-IP $remote_addr;
   proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
   
   # Upload file size limit
   client_max_body_size 100M;
   
   # Timeout settings
   proxy_connect_timeout 60s;
   proxy_send_timeout 60s;
   proxy_read_timeout 60s;
   ```

## 🔧 Konfigurasi

### Environment Variables (.env.docker)
```env
APP_URL=https://your-domain.com
TRUSTED_PROXIES=*
TRUST_HOSTS=true
```

### Upload File Configuration
Aplikasi sudah dikonfigurasi untuk menerima upload file hingga 100MB. Jika perlu lebih besar, update:

1. **Nginx Proxy Manager:** `client_max_body_size 200M;`
2. **PHP (Dockerfile):** Tambahkan di RUN command:
   ```dockerfile
   RUN echo "upload_max_filesize = 200M" >> /usr/local/etc/php/conf.d/uploads.ini \
       && echo "post_max_size = 200M" >> /usr/local/etc/php/conf.d/uploads.ini
   ```

## 🐛 Troubleshooting

### Error: "Bad Request - Size of request header field exceeds server limit"
✅ **Sudah diatasi** - Apache dikonfigurasi dengan `LimitRequestFieldSize 32768`

### Error Upload File Gagal di HTTPS
✅ **Sudah diatasi** - Proxy headers dikonfigurasi dengan benar:
- `X-Forwarded-Proto: https`
- `X-Forwarded-Host: domain.com`
- `TRUSTED_PROXIES=*`

### Error 500 pada Upload
1. Cek logs: `docker-compose logs app`
2. Pastikan folder storage writable:
   ```bash
   docker exec herba-scan-app chown -R www-data:www-data /var/www/html/storage
   docker exec herba-scan-app chmod -R 775 /var/www/html/storage
   ```

### Database Connection Error
1. Pastikan container database berjalan: `docker-compose ps`
2. Cek logs database: `docker-compose logs db`

## 📁 Struktur File

```
herba-scan-dashboard/
├── Dockerfile                 # Container configuration
├── docker-compose.yml        # Services configuration  
├── deploy.sh                 # Production deployment script
├── docker/
│   ├── apache/
│   │   └── 000-default.conf  # Apache virtual host config
│   └── entrypoint.sh         # Container startup script
├── .env.docker              # Production environment
└── README-DOCKER.md         # This file
```

## 🔐 Security Notes

1. **Production Environment:**
   - `APP_DEBUG=false`
   - `APP_ENV=production`
   - Generate unique `APP_KEY`

2. **Database Security:**
   - Ganti password default di `docker-compose.yml`
   - Gunakan strong password untuk production

3. **Proxy Security:**
   - Konfigurasi `TRUSTED_PROXIES` sesuai network Anda
   - Enable SSL/TLS di Nginx Proxy Manager

## 📞 Support

Jika mengalami masalah:
1. Cek logs container: `docker-compose logs [service-name]`
2. Verifikasi konfigurasi network Docker
3. Pastikan port tidak conflicted dengan service lain

## 🔄 Update Application

```bash
# Pull latest changes
git pull origin main

# Rebuild containers
docker-compose down
docker-compose build --no-cache
docker-compose up -d

# Run migrations if needed
docker exec herba-scan-app php artisan migrate --force
```