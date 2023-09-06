## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --no-cache` to build fresh images
3. Run `docker compose up` to start
4. Run `docker compose down --remove-orphans` to stop the Docker containers.

### PHPUnit JetBrains configuration
Test Framework: 
 - Use composer autoloader
 - Path to script:  /var/www/html//vendor/autoload.php

### PHP Interpreter
       from the docker: commissions_app:latest

Include Path from local: 

    - vendor/phpunit
    - app 

### @todo Would be nice to have:
- pagination for large files
- cache for bin and rate exchange lists to avoid to large datasets