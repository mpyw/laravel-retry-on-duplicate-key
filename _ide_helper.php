<?php

namespace Illuminate\Database
{
    class Connection
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
}
