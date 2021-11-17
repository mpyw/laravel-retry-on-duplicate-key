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
     *
     * @phpstan-template TReturn
     * @phpstan-template TArgs
     * @phpstan-param callable(TArgs): TReturn $callback
     * @phpstan-param TArgs ...$args
     * @phpstan-return TReturn
     */
    public function retryOnDuplicateKey(callable $callback, ...$args)
    {
        $connection = $this;

        \assert($connection instanceof ConnectionInterface);

        return (new RetryOnDuplicateKey($connection))($callback, ...$args);
    }
}
