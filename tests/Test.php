<?php

namespace Mpyw\LaravelRetryOnDuplicateKey\Tests;

use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
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
     */
    protected function getPackageProviders($app): array
    {
        return [];
    }

    protected function setUp(): void
    {
        parent::setUp();

        config(['database.connections' => require __DIR__ . '/config/database.php']);
        config(['database.default' => getenv('DB') ?: 'sqlite']);

        dump('Before creating schema');
        dump($this->db()->getPdo());

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });
        Schema::create('posts', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('posts');

        parent::tearDown();
    }

    protected function db(): Connection
    {
        return $this->app->make(Connection::class);
    }

    public function testInsertingWithImplicitId(): void
    {
        $this->assertSame(
            \PDO::ERRMODE_EXCEPTION,
            $this->db()->getPdo()->getAttribute(\PDO::ATTR_ERRMODE),
        );

        $this->assertSame(1, User::query()->create()->getKey());

        $this->assertSame(
            \PDO::ERRMODE_EXCEPTION,
            $this->db()->getPdo()->getAttribute(\PDO::ATTR_ERRMODE),
        );
    }

    public function testInsertingWithExplicitId(): void
    {
        $this->assertSame(
            \PDO::ERRMODE_EXCEPTION,
            $this->db()->getPdo()->getAttribute(\PDO::ATTR_ERRMODE),
        );

        $this->assertSame(0, Post::query()->create(['id' => 1])->getKey());

        // BUG!!!
        $this->assertSame(
            \PDO::ERRMODE_SILENT,
            $this->db()->getPdo()->getAttribute(\PDO::ATTR_ERRMODE),
        );
    }
}
