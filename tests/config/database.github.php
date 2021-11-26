<?php

declare(strict_types=1);

return [
    'mysql' => [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'testing',
        'username' => 'testing',
        'password' => 'testing',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,
        'engine' => null,
    ],
    'pgsql' => [
        'driver' => 'pgsql',
        'host' => '127.0.0.1',
        'port' => '5432',
        'database' => 'testing',
        'username' => 'testing',
        'password' => 'testing',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'public',
        'sslmode' => 'prefer',
    ],
    'sqlite' => [
        'driver' => 'sqlite',
        'database' => ':memory:',
        'prefix' => '',
    ],
    'sqlsrv' => [
        'driver' => 'sqlsrv',
        'host' => '127.0.0.1',
        'port' => '1433',
        'database' => 'testing',
        'username' => 'sa',
        'password' => 'Password!',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'odbc' => (bool)getenv('ENABLE_ODBC'),
        'odbc_datasource_name' => 'Driver={ODBC Driver 17 for SQL Server};Server=127.0.0.1;Database=testing;UID=sa;PWD=Password!',
    ],
];
