<?php

declare(strict_types=1);

namespace Mpyw\LaravelRetryOnDuplicateKey;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\PostgresConnection;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Database\SqlServerConnection;
use LogicException;
use Mpyw\UniqueViolationDetector\MySQLDetector;
use Mpyw\UniqueViolationDetector\PostgresDetector;
use Mpyw\UniqueViolationDetector\SQLiteDetector;
use Mpyw\UniqueViolationDetector\SQLServerDetector;
use Mpyw\UniqueViolationDetector\UniqueViolationDetector;
use PDOException;

class UniqueConstraintViolationDetector
{
    public static function uniqueConstraintViolated(ConnectionInterface $connection, PDOException $e): bool
    {
        return self::detector($connection)->uniqueConstraintViolated($e);
    }

    private static function detector(ConnectionInterface $connection): UniqueViolationDetector
    {
        if ($connection instanceof MySqlConnection) {
            return new MySQLDetector();
        }
        if ($connection instanceof PostgresConnection) {
            return new PostgresDetector();
        }
        if ($connection instanceof SQLiteConnection) {
            return new SQLiteDetector();
        }
        if ($connection instanceof SqlServerConnection) {
            return new SQLServerDetector();
        }

        // @codeCoverageIgnoreStart
        throw new LogicException('Unsupported Driver');
        // @codeCoverageIgnoreEnd
    }
}
