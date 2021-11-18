<?php

namespace Illuminate\Database
{
    if (false) {
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

        class Connection implements ConnectionInterface
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
}

namespace Illuminate\Support\Facades
{
    if (false) {
        class DB extends Facade
        {
            /**
             * Retries once on duplicate key errors.
             *
             * @param mixed  ...$args
             * @return mixed
             * @see \Mpyw\LaravelRetryOnDuplicateKey\RetriesOnDuplicateKey
             */
            public static function retryOnDuplicateKey(callable $callback, ...$args)
            {
            }
        }
    }
}
