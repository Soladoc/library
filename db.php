<?php
const DB_SERVER = 'postgresdb';
const DB_DRIVER = 'pgsql';
const DB_NAME = 'postgres';
const DB_USER   = 'sae';

function db_connect(): PDO {
    return new PDO(
        DB_DRIVER.':host='.DB_SERVER.';dbname='.DB_NAME,
        DB_USER,
        getenv('DB_ROOT_PASSWORD')
    );
}