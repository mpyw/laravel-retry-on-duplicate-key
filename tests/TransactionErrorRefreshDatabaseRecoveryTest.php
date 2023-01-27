<?php

declare(strict_types=1);

namespace Mpyw\LaravelRetryOnDuplicateKey\Tests;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mpyw\LaravelRetryOnDuplicateKey\Tests\Models\User;
use Throwable;

class TransactionErrorRefreshDatabaseRecoveryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws Throwable
     */
    public function testRecoveryFromTransactionAbortedError(): void
    {
        try {
            $this->db()->retryOnDuplicateKey(function () {
                static $tries = 0;

                $this->assertSame((bool)$tries++, $this->db()->hasModifiedRecords());

                $user = new User();
                $user->fill(['id' => 2, 'email' => 'example@example.com', 'type' => 'consumer'])->save();
            });
        } catch (QueryException $e) {
            var_dump($e->errorInfo);
            $this->assertCount(2, $this->queries);
        }

        $user = new User();
        $user->fill(['id' => 2, 'email' => 'example-another@example.com', 'type' => 'consumer'])->save();
        $this->assertCount(3, $this->queries);
    }
}
