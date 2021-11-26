<?php

declare(strict_types=1);

namespace Mpyw\LaravelRetryOnDuplicateKey\Tests;

use Illuminate\Database\Connection;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Mpyw\LaravelRetryOnDuplicateKey\ConnectionServiceProvider;
use Mpyw\LaravelRetryOnDuplicateKey\Tests\Models\Post;
use Mpyw\LaravelRetryOnDuplicateKey\Tests\Models\User;
use Orchestra\Testbench\TestCase as BaseTestCase;

class Test extends BaseTestCase
{
    /**
     * @var string[]
     */
    protected array $queries = [];

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return string[]
     */
    protected function getPackageProviders($app): array
    {
        return [ConnectionServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        config(['database.connections' => require __DIR__ . '/config/database.php']);
        config(['database.default' => getenv('DB') ?: 'sqlite']);

        if ($this->db()->getDriverName() === 'sqlite') {
            $this->db()->statement('PRAGMA foreign_keys=true;');
        }

        Schema::create('users', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('email')->unique();
            $table->enum('type', ['consumer', 'provider']);
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });

        $user = new User();
        $user->fill(['id' => 1, 'email' => 'example@example.com', 'type' => 'consumer'])->save();

        $this->db()->forgetRecordModificationState();

        $this->db()->beforeExecuting(function (string $query) {
            $this->queries[] = $query;
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('posts');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    protected function db(): Connection
    {
        $connection = $this->app->make(Connection::class);
        assert($connection instanceof Connection);
        return $connection;
    }

    public function testRetryOnDuplicatePrimaryKey(): void
    {
        try {
            $this->db()->retryOnDuplicateKey(function () {
                static $tries = 0;

                $this->assertSame((bool)$tries++, $this->db()->hasModifiedRecords());

                $user = new User();
                $user->fill(['id' => 1, 'email' => 'example-another@example.com', 'type' => 'consumer'])->save();
            });
        } catch (QueryException $e) {
        }

        $this->assertTrue(isset($e));
        $this->assertCount(2, $this->queries);
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
        } catch (QueryException $e) {
        }

        $this->assertTrue(isset($e));
        $this->assertCount(2, $this->queries);
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
        } catch (QueryException $e) {
        }

        $this->assertTrue(isset($e));
        $this->assertCount(1, $this->queries);
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
        } catch (QueryException $e) {
        }

        $this->assertTrue(isset($e));
        $this->assertCount(1, $this->queries);
    }
}
