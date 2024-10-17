<?php
function db_connect(): PDO {
    return new PDO(
        'psgsql:host=postgresdb;port='.getenv('PGDB_PORT').';dbname=postgres',
        getenv('DB_USER'),
        getenv('DB_ROOT_PASSWORD')
    );
}