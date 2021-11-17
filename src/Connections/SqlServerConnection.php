<?php

namespace Mpyw\LaravelRetryOnDuplicateKey\Connections;

use Illuminate\Database\SqlServerConnection as BaseSqlServerConnection;
use Mpyw\LaravelRetryOnDuplicateKey\RetriesOnDuplicateKey;

class SqlServerConnection extends BaseSqlServerConnection
{
    use RetriesOnDuplicateKey;
}
