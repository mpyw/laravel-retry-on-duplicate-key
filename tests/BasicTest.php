<?php

declare(strict_types=1);

namespace Mpyw\LaravelRetryOnDuplicateKey\Tests;

use Illuminate\Database\QueryException;
use Mpyw\LaravelRetryOnDuplicateKey\Tests\Models\Post;
use Mpyw\LaravelRetryOnDuplicateKey\Tests\Models\User;

class BasicTest extends TestCase
{
    public function testRetryOnDuplicatePrimaryKey(): void
    {
        try {
            $this->db()->retryOnDuplicateKey(function () {
                static $tries = 0;

                $this->assertSame((bool)$tries++, $this->db()->hasModifiedRecords());

                $user = new User();
                $user->fill(['id' => 1, 'email' => 'example-another@example.com', 'type' => 'consumer'])->save();
            });
            $this->fail();
        } catch (QueryException $e) {
            var_dump($e->errorInfo);
            $this->assertCount(2, $this->queries);
        }
    }

    public function testRetryOnDuplicateUniqueKey(): void
    {
        try {
            $this->db()->retryOnDuplicateKey(function () {
                static $tries = 0;

                $this->assertSame((bool)$tries++, $this->db()->hasModifiedRecords());

                $user = new User();
                $user->fill(['id' => 2, 'email' => 'example@example.com', 'type' => 'consumer'])->save();
            });
            $this->fail();
        } catch (QueryException $e) {
            var_dump($e->errorInfo);
            $this->assertCount(2, $this->queries);
        }
    }

    public function testDontRetryOnForeignKeyConstraintViolation(): void
    {
        try {
            $this->db()->retryOnDuplicateKey(function () {
                static $tries = 0;

                $this->assertSame(0, $tries++);
                $this->assertFalse($this->db()->hasModifiedRecords());

                $post = new Post();
                $post->fill(['user_id' => 9999])->save();
            });
            $this->fail();
        } catch (QueryException $e) {
            var_dump($e->errorInfo);
            $this->assertCount(1, $this->queries);
        }
    }

    public function testDontRetryOnEnumConstraintViolation(): void
    {
        try {
            $this->db()->retryOnDuplicateKey(function () {
                static $tries = 0;

                $this->assertSame(0, $tries++);
                $this->assertFalse($this->db()->hasModifiedRecords());

                $user = new User();
                $user->fill(['id' => 2, 'email' => 'example-another@example.com', 'type' => 'foo'])->save();
            });
            $this->fail();
        } catch (QueryException $e) {
            var_dump($e->errorInfo);
            $this->assertCount(1, $this->queries);
        }
    }
}
