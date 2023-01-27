<?php

declare(strict_types=1);

namespace Mpyw\LaravelRetryOnDuplicateKey\Tests;

use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Mpyw\LaravelRetryOnDuplicateKey\ConnectionServiceProvider;
use Mpyw\LaravelRetryOnDuplicateKey\Tests\Models\User;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
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

    protected function getEnvironmentSetUp($app): void
    {
        config([
            'database.connections' => require __DIR__ . '/config/database.php',
            'database.default' => getenv('DB') ?: 'sqlite',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        if ($this->db()->getDriverName() === 'sqlite') {
            $this->db()->statement('PRAGMA foreign_keys=true;');
        }

        // Workaround for https://github.com/laravel/framework/pull/35988
        $shouldRestartTransaction = false;
        if ($this->db()->getPdo()->inTransaction()) {
            $this->db()->getPdo()->commit();
            $shouldRestartTransaction = true;
        }

        Schema::dropIfExists('posts');
        Schema::dropIfExists('users');

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

        // Workaround for https://github.com/laravel/framework/pull/35988
        if ($shouldRestartTransaction) {
            $this->db()->getPdo()->beginTransaction();
        }

        $user = new User();
        $user->fill(['id' => 1, 'email' => 'example@example.com', 'type' => 'consumer'])->save();

        $this->db()->forgetRecordModificationState();

        $this->db()->beforeExecuting(function (string $query) {
            $this->queries[] = $query;
        });

        $this->queries = [];
    }

    protected function db(): Connection
    {
        $connection = App::make(Connection::class);
        assert($connection instanceof Connection);
        return $connection;
    }
}
