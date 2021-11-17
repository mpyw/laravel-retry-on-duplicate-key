# Laravel Retry on Duplicate Key [![Build Status](https://github.com/mpyw/laravel-retry-on-duplicate-key/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/mpyw/laravel-retry-on-duplicate-key/actions) [![Coverage Status](https://coveralls.io/repos/github/mpyw/laravel-retry-on-duplicate-key/badge.svg?branch=master)](https://coveralls.io/github/mpyw/laravel-retry-on-duplicate-key?branch=master)

Automatically retry **non-atomic** upsert operation when unique constraints are violated.

e.g. `firstOrCreate()` `updateOrCreate()` `firstOrNew() + save()` 

Original Issue:  [Duplicate entries on updateOrCreate · Issue #19372 · laravel/framework](https://github.com/laravel/framework/issues/19372#issuecomment-584676368)

## Installing

```
composer require mpyw/laravel-retry-on-duplicate-key
```

## Basic usage

The default implementation is provided by `ConnectionServiceProvider`, however, **package discovery is not available**.
Be careful that you MUST register it in **`config/app.php`** by yourself.

```php
<?php

return [

    /* ... */

    'providers' => [
        /* ... */

        Mpyw\LaravelRetryOnDuplicateKey\ConnectionServiceProvider::class,

        /* ... */
    ],

];
```

```php
<?php

use Illuminate\Support\Facades\DB;

$user = DB::retryOnDuplicateKey(function () {
    // Email has a unique constraint
    return User::firstOrCreate(['email' => 'example.com'], ['name' => 'Example']);
});
```

| OTHER | YOU |
|:----:|:----:|
| SELECT<br>(No Results) | |
| ︙ | |
| ︙ | SELECT<br>(No Results) |
| ︙ | ︙ |
| INSERT<br>(OK) | ︙ |
| | ︙ |
| | INSERT<br>(Error! Duplicate entry) |
| | Prepare for the next retry, referring to primary connection |
| | SELECT<br>(1 Result) |


## Advanced Usage

You can extend Connection classes with `RetriesOnDuplicateKey` trait by yourself.

```php
<?php

namespace App\Providers;

use App\Database\MySqlConnection;
use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Connection::resolverFor('mysql', function (...$parameters) {
            return new MySqlConnection(...$parameters);
        });
    }
}
```

```php
<?php

namespace App\Database;

use Illuminate\Database\Connection as BaseMySqlConnection;
use Mpyw\LaravelRetryOnDuplicateKey\RetriesOnDuplicateKey;

class MySqlConnection extends BaseMySqlConnection
{
    use RetriesOnDuplicateKey;
}
```

## Differences from other native upsert implementations

- [[8.x] Add upsert to Eloquent and Base Query Builders by paras-malhotra · Pull Request #34698 · laravel/framework](https://github.com/laravel/framework/pull/34698)
- [staudenmeir/laravel-upsert: Laravel UPSERT and INSERT IGNORE queries](https://github.com/staudenmeir/laravel-upsert)

These implementations are focused on atomically performing INSERT queries. Hence, they have the following problems.

- The INSERT query is always executed, which ruins the **`sticky`** optimization when the connection has both Reader (Replica) and Writer (Primary).
- The SELECT query is not executed, so the results cannot be retrieved.

This library is a wise choice if your queries complete mostly with only 1 SELECT, rarely with 1 INSERT + 1 SELECT.
