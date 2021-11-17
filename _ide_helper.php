<?php

namespace Illuminate\Database
{
    interface ConnectionInterface
    {
        /**
         * Retries once on duplicate key errors.
         *
         * @param mixed  ...$args
         * @return mixed
         * @see \Mpyw\LaravelRetryOnDuplicateKey\RetriesOnDuplicateKey
         *
         * @phpstan-template TReturn
         * @phpstan-template TArgs
         * @phpstan-param callable(TArgs): TReturn $callback
         * @phpstan-param TArgs ...$args
         * @phpstan-return TReturn
         */
        public function retryOnDuplicateKey(callable $callback, ...$args);
    }

    class Connection implements \Illuminate\Database\ConnectionInterface
    {
        /**
         * Retries once on duplicate key errors.
         *
         * @param mixed  ...$args
         * @return mixed
         * @see \Mpyw\LaravelRetryOnDuplicateKey\RetriesOnDuplicateKey
         *
         * @phpstan-template TReturn
         * @phpstan-template TArgs
         * @phpstan-param callable(TArgs): TReturn $callback
         * @phpstan-param TArgs ...$args
         * @phpstan-return TReturn
         */
        public function retryOnDuplicateKey(callable $callback, ...$args)
        {
        }
    }
}
