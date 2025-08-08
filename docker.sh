#!/bin/bash
set -e

readonly PROJECT_NAME="aeduca"
readonly COMPOSE_FILE="docker-compose.yml"

check_docker() {
    command -v docker &> /dev/null || { echo "Docker not installed"; exit 1; }
    command -v docker-compose &> /dev/null || { echo "Docker Compose not installed"; exit 1; }
    docker info &> /dev/null || { echo "Docker daemon not running"; exit 1; }
}

check_compose_file() {
    [[ -f "$COMPOSE_FILE" ]] || { echo "Docker Compose file not found"; exit 1; }
}

check_containers() {
    docker-compose ps | grep -q "Up" || { echo "Containers not running. Run: ./docker.sh up"; exit 1; }
}

cmd_build() {
    docker-compose -f "$COMPOSE_FILE" build
}

cmd_up() {
    docker-compose -f "$COMPOSE_FILE" up -d
}

cmd_down() {
    docker-compose -f "$COMPOSE_FILE" down
}

cmd_restart() {
    cmd_down && cmd_up
}

cmd_logs() {
    docker-compose -f "$COMPOSE_FILE" logs -f "${2:-}"
}

cmd_status() {
    docker-compose -f "$COMPOSE_FILE" ps
}

cmd_shell() {
    docker exec -it "${PROJECT_NAME}-app" sh
}

cmd_artisan() {
    shift
    check_containers
    docker exec -it "${PROJECT_NAME}-app" php artisan "$@"
}

cmd_composer() {
    shift
    check_containers
    docker exec -it "${PROJECT_NAME}-app" composer "$@"
}

cmd_npm() {
    shift
    check_containers
    docker exec -it "${PROJECT_NAME}-app" npm "$@"
}

cmd_migrate() {
    check_containers
    docker exec -it "${PROJECT_NAME}-app" php artisan migrate
}

cmd_seed() {
    check_containers
    docker exec -it "${PROJECT_NAME}-app" php artisan db:seed
}

cmd_fresh() {
    check_containers
    docker exec -it "${PROJECT_NAME}-app" php artisan migrate:fresh --seed
}

cmd_setup() {
    echo "Database setup..."
    docker-compose stop postgres 2>/dev/null || true
    docker volume rm main_postgres_data 2>/dev/null || true
    docker-compose up -d postgres
    echo "Done!"
}

cmd_db_shell() {
    docker exec -it "${PROJECT_NAME}-postgres" psql -U elias -d aeduca
}

cmd_dev() {
    check_containers
    docker exec -it "${PROJECT_NAME}-app" npm run dev
}

cmd_test() {
    check_containers
    docker exec -it "${PROJECT_NAME}-app" npm run test
}

show_help() {
    cat << EOF
Aeduca Docker Manager

Usage: ./docker.sh [command]

Quick Start: ./docker.sh build && ./docker.sh up

Commands:
  build           Build containers
  up              Start all services
  down            Stop all services
  restart         Restart all services
  logs            Show logs (optional: service name)
  status          Show container status

App:
  shell           Access app container
  artisan         Run Laravel Artisan commands
  composer        Run Composer commands
  npm             Run NPM commands

Database:
  migrate         Run Laravel migrations
  seed            Run database seeders
  fresh           Fresh migration with seeders
  setup           Reset database with init scripts
  db:shell        PostgreSQL shell

Development:
  dev             Start Vite dev server
  test            Run tests
EOF
}

main() {
    check_docker
    check_compose_file

    case "${1:-help}" in
        "build")        cmd_build ;;
        "up")           cmd_up ;;
        "down")         cmd_down ;;
        "restart")      cmd_restart ;;
        "logs")         cmd_logs "$@" ;;
        "status")       cmd_status ;;
        "shell")        cmd_shell ;;
        "artisan")      cmd_artisan "$@" ;;
        "composer")     cmd_composer "$@" ;;
        "npm")          cmd_npm "$@" ;;
        "migrate")      cmd_migrate ;;
        "seed")         cmd_seed ;;
        "fresh")        cmd_fresh ;;
        "setup")        cmd_setup ;;
        "db:shell")     cmd_db_shell ;;
        "dev")          cmd_dev ;;
        "test")         cmd_test ;;
        "help"|"-h"|"--help") show_help ;;
        *)              echo "Unknown command: $1"; show_help; exit 1 ;;
    esac
}

main "$@"
