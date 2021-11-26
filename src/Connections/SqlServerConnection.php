<?php

declare(strict_types=1);

namespace Mpyw\LaravelRetryOnDuplicateKey\Connections;

use Illuminate\Database\SqlServerConnection as BaseSqlServerConnection;
use Mpyw\LaravelRetryOnDuplicateKey\RetriesOnDuplicateKey;

class SqlServerConnection extends BaseSqlServerConnection
{
    use RetriesOnDuplicateKey;
}
