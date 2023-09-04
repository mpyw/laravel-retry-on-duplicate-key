# Laravel Retry on Duplicate Key [![Build Status](https://github.com/mpyw/laravel-retry-on-duplicate-key/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/mpyw/laravel-retry-on-duplicate-key/actions) [![Coverage Status](https://coveralls.io/repos/github/mpyw/laravel-retry-on-duplicate-key/badge.svg?branch=master)](https://coveralls.io/github/mpyw/laravel-retry-on-duplicate-key?branch=master)

**ABANDONED: Due to changes in Laravel [v10.20.0](https://github.com/laravel/framework/releases/tag/v10.20.0), [v10.21.0](https://github.com/laravel/framework/releases/tag/v10.21.0) and [v10.21.1](https://github.com/laravel/framework/releases/tag/v10.21.1), the functionalities of this library have been integrated into the Laravel core, making the library unnecessary in most cases. Therefore, maintenance will be discontinued. <ins>From now on, retry processing will be automatically performed in `Model::createOrFirst()`, `Model::firstOrCreate()`, and `Model::updateOrCreate()`</ins>. The only pattern that still has value is for `Model::firstOrNew() + save()`, but since equivalent processing can be written by yourself, please do not use this library anymore.**

Automatically retry **non-atomic** upsert operation when unique constraints are violated.

e.g. `firstOrCreate()` `updateOrCreate()` `firstOrNew() + save()` 

Original Issue:  [Duplicate entries on updateOrCreate · Issue #19372 · laravel/framework](https://github.com/laravel/framework/issues/19372#issuecomment-584676368)

## Requirements

| Package                                                                                             | Version                              | Mandatory |
|:----------------------------------------------------------------------------------------------------|:-------------------------------------|:---------:|
| PHP                                                                                                 | <code>^8.0</code>                    |     ✅     |
| Laravel                                                                                             | <code>^9.0 &#124;&#124; ^10.0</code> |     ✅     |
| [mpyw/laravel-unique-violation-detector](https://github.com/mpyw/laravel-unique-violation-detector) | <code>^1.0</code>                    |     ✅     |
| PHPStan                                                                                             | <code>&gt;=1.1</code>                |           |

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

These implementations are focused on atomically performing **INSERT-or-UPDATE** queries. Hence, there are definitely clear differences in usage.

- `firstOrCreate()` `updateOrCreate()` save **auto-increment numbering spaces** efficiently.
  - In contrast, `upsert()` always increments the number even if no INSERT occurs and it leads to yield missing numbers. This can be a serious problem if the query is executed frequently. If you still want to go along with `upsert()`, you may need to consider using UUID or ULID instead of auto-increment.
- `firstOrCreate()` has clear advantages if its call completes **mostly with only one SELECT** and rarely with succeeding one INSERT.
  - In contrast, you must always execute two queries in all cases with `upsert()`.
- As for `updateOrCreate()`, there may be extra considerations depending on RDBMS.
  - For RDBMS other than MySQL, `updateOrCreate()` would be better unless its call definitely changes field values on rows. `upsert()` may ruin the **[`sticky`](https://laravel.com/docs/8.x/database#read-and-write-connections)** optimization when the connection has both Reader (Replica) and Writer (Primary) because they assume that all rows narrowed by WHERE conditions have been affected.
  - In MySQL, `upsert()` has no limitations about that. It regards that only rows are affected whose field values are actually changed.
- Be careful that `upsert()` never triggers Eloquent events such as `created` or `updated` because its implementation is on Eloquent Builder, not on Model.
- Only `upsert()` supports bulk insert. It is beneficial if there are a large number of records and you don't need any Eloquent events.
