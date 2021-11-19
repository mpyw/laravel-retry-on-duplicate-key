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
        // SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry
        return $e->getCode() === '23000' && ($e->errorInfo[1] ?? 0) === 1062;
    }

    protected static function postgres(PDOException $e): bool
    {
        // SQLSTATE[23505]: Unique violation: 7 ERROR:  duplicate key value violates unique constraint
        return $e->getCode() === '23505';
    }

    protected static function sqlite(PDOException $e): bool
    {
        // SQLite returns SQLSTATE[23000] and 19 (SQLITE_CONSTRAINT) on all constraint violations.
        // So we need to check messages.
        return $e->getCode() === '23000'
            && ($e->errorInfo[1] ?? 0) === 19
            && Str::startsWith($e->errorInfo[2] ?? '', 'UNIQUE constraint failed');
    }

    protected static function sqlserver(PDOException $e): bool
    {
        switch ($e->getCode()) {
            // The following drivers correctly return error codes.
            //
            // - pdo_sqlsrv (SQLSTATE[23000]: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]...)
            // - pdo_odbc (SQLSTATE[23000]: Integrity constraint violation: (...) [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]...)
            case '23000':
                return \in_array($e->errorInfo[1] ?? 0, [2627, 2601], true);

            // pdo_dblib returns SQLSTATE[HY000] and 20018 (General Error) on all constraint violations.
            // So we need to check messages.
            case 'HY000':
            default:
                return ($e->errorInfo[1] ?? 0) === 20018
                    && Str::startsWith($e->errorInfo[2] ?? '', [
                        'Violation of PRIMARY KEY constraint',
                        'Cannot insert duplicate key row',
                    ]);
        }
    }
}
