<?php

namespace Mpyw\LaravelRetryOnDuplicateKey\Connections;

use Illuminate\Database\MySqlConnection as BaseMySqlConnection;
use Mpyw\LaravelRetryOnDuplicateKey\RetriesOnDuplicateKey;

class MySqlConnection extends BaseMySqlConnection
{
    use RetriesOnDuplicateKey;
}
