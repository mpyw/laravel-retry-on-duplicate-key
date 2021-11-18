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
         */
        public function retryOnDuplicateKey(callable $callback, ...$args)
        {
        }
    }
}
