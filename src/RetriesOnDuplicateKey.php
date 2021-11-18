<?php

namespace Mpyw\LaravelRetryOnDuplicateKey;

use Illuminate\Database\ConnectionInterface;

trait RetriesOnDuplicateKey
{
    /**
     * Retries once on duplicate key errors.
     *
     * @param mixed  ...$args
     * @return mixed
     */
    public function retryOnDuplicateKey(callable $callback, ...$args)
    {
        $connection = $this;

        \assert($connection instanceof ConnectionInterface);

        return (new RetryOnDuplicateKey($connection))($callback, ...$args);
    }
}
