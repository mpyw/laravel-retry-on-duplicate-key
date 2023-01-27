<?php

declare(strict_types=1);

namespace Mpyw\LaravelRetryOnDuplicateKey;

use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\PostgresConnection;
use Mpyw\LaravelUniqueViolationDetector\UniqueViolationDetector;
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
     * @param mixed ...$args
     * @return mixed
     */
    public function __invoke(callable $callback, ...$args)
    {
        try {
            return $this->withSavepoint(fn () => $callback(...$args));
        } catch (PDOException $e) {
            if ((new UniqueViolationDetector($this->connection))->violated($e)) {
                $this->forceReferringPrimaryConnection();
                return $this->withSavepoint(fn () => $callback(...$args));
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

    /**
     * @phpstan-template T
     * @phpstan-param callable(): T $callback
     * @phpstan-return T
     * @return mixed
     * @noinspection PhpDocMissingThrowsInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    protected function withSavepoint(callable $callback)
    {
        return $this->needsSavepoint()
            ? $this->connection->transaction(fn () => $callback())
            : $callback();
    }

    protected function needsSavepoint(): bool
    {
        // In Postgres, savepoints allow recovery from errors.
        // This ensures retrying should work also in transactions.
        return $this->connection instanceof PostgresConnection
            && $this->connection->transactionLevel() > 0;
    }
}
