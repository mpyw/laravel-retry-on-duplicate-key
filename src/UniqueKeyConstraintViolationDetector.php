<?php

namespace Mpyw\LaravelRetryOnDuplicateKey;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\PostgresConnection;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Database\SqlServerConnection;
use Illuminate\Support\Str;
use LogicException;
use PDOException;

class UniqueKeyConstraintViolationDetector
{
    public static function uniqueKeyConstraintViolated(ConnectionInterface $connection, PDOException $e): bool
    {
        if ($connection instanceof MySqlConnection) {
            return static::mysql($e);
        }
        if ($connection instanceof PostgresConnection) {
            return static::postgres($e);
        }
        if ($connection instanceof SQLiteConnection) {
            return static::sqlite($e);
        }
        if ($connection instanceof SqlServerConnection) {
            return static::sqlserver($e);
        }

        // @codeCoverageIgnoreStart
        throw new LogicException('Unsupported Driver');
        // @codeCoverageIgnoreEnd
    }

    protected static function mysql(PDOException $e): bool
    {
        return Str::startsWith(
            $e->getMessage(),
            'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry',
        );
    }

    protected static function postgres(PDOException $e): bool
    {
        // SQLSTATE[23505]: Unique violation: 7 ERROR:  duplicate key value violates unique constraint
        return $e->getCode() === '23505';
    }

    protected static function sqlite(PDOException $e): bool
    {
        return Str::startsWith(
            $e->getMessage(),
            'SQLSTATE[23000]: Integrity constraint violation: 19 UNIQUE constraint failed',
        );
    }

    protected static function sqlserver(PDOException $e): bool
    {
        return Str::startsWith(
            $e->getMessage(),
            [
                'SQLSTATE[HY000]: General error: 20018 Violation of PRIMARY KEY constraint',
                'SQLSTATE[HY000]: General error: 20018 Cannot insert duplicate key row',
            ],
        );
    }
}
