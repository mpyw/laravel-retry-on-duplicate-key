<?php

namespace Mpyw\LaravelRetryOnDuplicateKey\Connections;

use Illuminate\Database\PostgresConnection as BasePostgresConnection;
use Mpyw\LaravelRetryOnDuplicateKey\RetriesOnDuplicateKey;

class PostgresConnection extends BasePostgresConnection
{
    use RetriesOnDuplicateKey;
}
