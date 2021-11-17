<?php

namespace Mpyw\LaravelRetryOnDuplicateKey\Tests\Models;

use Illuminate\Database\SqlServerConnection;
use Mpyw\LaravelRetryOnDuplicateKey\RetriesOnDuplicateKey;

abstract class Model extends \Illuminate\Database\Eloquent\Model
{
    use RetriesOnDuplicateKey;

    protected $guarded = [];

    public function getDateFormat(): string
    {
        // https://github.com/laravel/nova-issues/issues/1796
        if ($this->getConnection() instanceof SqlServerConnection) {
            return 'Y-m-d H:i:s';
        }

        return parent::getDateFormat();
    }
}
