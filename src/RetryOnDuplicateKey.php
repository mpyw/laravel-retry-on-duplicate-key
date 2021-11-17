<?php

namespace Mpyw\LaravelRetryOnDuplicateKey;

use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;
use PDOException;

class RetryOnDuplicateKey
{
    protected ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

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
    public function __invoke(callable $callback, ...$args)
    {
        try {
            return $callback(...$args);
        } catch (PDOException $e) {
            if (UniqueKeyConstraintViolationDetector::uniqueKeyConstraintViolated($this->connection, $e)) {
                $this->forceReferringPrimaryConnection();
                return $callback(...$args);
            }
            throw $e;
        }
    }

    /**
     * Make sure to fetch the latest data on the next try.
     */
    protected function forceReferringPrimaryConnection(): void
    {
        $connection = $this->connection;

        if ($connection instanceof Connection) {
            $connection->recordsHaveBeenModified();
        }
    }
}
