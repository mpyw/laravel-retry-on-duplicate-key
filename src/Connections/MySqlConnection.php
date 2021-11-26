<?php

declare(strict_types=1);

namespace Mpyw\LaravelRetryOnDuplicateKey\Connections;

use Illuminate\Database\MySqlConnection as BaseMySqlConnection;
use Mpyw\LaravelRetryOnDuplicateKey\RetriesOnDuplicateKey;

class MySqlConnection extends BaseMySqlConnection
{
    use RetriesOnDuplicateKey;
}
