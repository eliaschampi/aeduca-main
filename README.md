# Aeduca Docker

> Clean, efficient Laravel application with PostgreSQL

[![Docker](https://img.shields.io/badge/Docker-Ready-blue?logo=docker)](https://docker.com)
[![Laravel](https://img.shields.io/badge/Laravel-10.x-red?logo=laravel)](https://laravel.com)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-blue?logo=postgresql)](https://postgresql.org)

## Quick Start

```bash
# Build and start
./docker.sh build
./docker.sh up

# Setup database (when needed)
./docker.sh setup

# Access application
open http://localhost
```

## Architecture

```
├── src/                 # Laravel application
├── docker-compose.yml   # Services orchestration
├── Dockerfile          # Laravel + Node image
├── nginx.conf          # Web server config
├── .env.docker         # Docker environment
└── docker.sh           # Management script
```

## Commands

### Core
| Command | Description |
|---------|-------------|
| `./docker.sh build` | Build containers |
| `./docker.sh up` | Start all services |
| `./docker.sh down` | Stop all services |
| `./docker.sh restart` | Restart all services |
| `./docker.sh status` | Show container status |
| `./docker.sh logs [service]` | Show logs (optional: service name) |

### App
| Command | Description |
|---------|-------------|
| `./docker.sh shell` | Access app container |
| `./docker.sh artisan [cmd]` | Run Laravel Artisan commands |
| `./docker.sh composer [cmd]` | Run Composer commands |
| `./docker.sh npm [cmd]` | Run NPM commands |

### Database
| Command | Description |
|---------|-------------|
| `./docker.sh migrate` | Run Laravel migrations |
| `./docker.sh seed` | Run database seeders |
| `./docker.sh fresh` | Fresh migration with seeders |
| `./docker.sh setup` | Reset database with init scripts |
| `./docker.sh db:shell` | PostgreSQL shell |

### Development
| Command | Description |
|---------|-------------|
| `./docker.sh dev` | Start Vite dev server |
| `./docker.sh test` | Run tests |

## Services

| Service | Container | Port | Description |
|---------|-----------|------|-------------|
| **Web** | `aeduca-nginx` | `:80` | Nginx web server |
| **App** | `aeduca-app` | `:9000` | Laravel + PHP-FPM |
| **Database** | `aeduca-postgres` | `:5432` | PostgreSQL 16 |
| **Cache** | `aeduca-redis` | `:6379` | Redis cache |

## Database

**Configuration:**
- **User:** `elias`
- **Password:** `7532`
- **Database:** `aeduca`
- **Locale:** `es_PE.utf8`
- **Extensions:** `btree_gin`, `pg_trgm`, `pgcrypto`
- **Init Scripts:** `src/database/init/*.sql`

## Development

### Laravel Commands
```bash
./docker.sh shell
composer install        # Install PHP dependencies
php artisan migrate     # Run migrations
php artisan tinker      # Interactive REPL
```

### Frontend Commands
```bash
./docker.sh shell
npm install             # Install Node dependencies
npm run dev            # Start Vite dev server
npm run build          # Build for production
```

### Useful Commands
```bash
# View logs
./docker.sh logs app

# Database access
docker exec -it aeduca-postgres psql -U elias -d aeduca

# Clear Redis cache
docker exec -it aeduca-redis redis-cli FLUSHALL
```

## Environment Configuration

### Environment Files
| File | Purpose | Committed |
|------|---------|-----------|
| `.env-example` | Template | ✅ Yes |
| `.env.docker` | Docker config | ❌ No |
| `src/.env-example` | Laravel template | ✅ Yes |
| `src/.env` | Laravel config | ❌ No |

### Consistent Configuration
All environment files use the same database credentials:

```bash
# Database Configuration
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=aeduca
DB_USERNAME=elias
DB_PASSWORD=7532

# Cache Configuration
REDIS_HOST=redis
REDIS_PORT=6379
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

## Troubleshooting

### Reset Database
```bash
./docker.sh setup
```

### Clear Laravel Cache
```bash
./docker.sh shell
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Fix Permissions
```bash
./docker.sh shell
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

## License

MIT License. See `LICENSE` for details.
