# Об этом форке 

Так как в облаке по видимому забили на поддержку библиотеки, временно перенёс в эту ветку нужные изменения
для установки использовать

```bash
  composer require dimajolkin/ydb-php-sdk "^1.3"
```

1. https://github.com/bashkarev/ydb-php-sdk/tree/anonymous - возможность ананимной авторизации
2. https://github.com/yandex-cloud/ydb-php-sdk/pull/22


YDB PHP SDK provides access to Yandex Database cloud services from PHP code.

Yandex Database is a distributed fault-tolerant DBMS with high availability and scalability, strict consistency and ACID. An SQL dialect – YQL – is used for queries.

Yandex Database is available in two modes:

- Serverless computing mode (only performed operations are paid);
- Dedicated instance mode (dedicated computing resources are paid).

# Documentation

[https://cloud.yandex.ru/docs/ydb/](https://cloud.yandex.ru/docs/ydb/)

# Installation

The recommended method of installing is Composer.

Run the following:

```bash
composer require yandex-cloud/ydb-php-sdk
```

# Connection

First, create a database using [Yandex Cloud Console](https://cloud.yandex.ru/docs/ydb/quickstart/create-db).

Yandex Database supports the following authentication methods:

- OAuth token
- JWT + private key
- JWT + JSON file
- Metadata URL
- Anonymous

## OAuth token

You should obtain [a new OAuth token](https://cloud.yandex.ru/docs/iam/concepts/authorization/oauth-token).

Use your OAuth token:

```php
<?php

use YandexCloud\Ydb\Ydb;

$config = [

    // Database path
    'database'    => '/ru-central1/b1glxxxxxxxxxxxxxxxx/etn0xxxxxxxxxxxxxxxx',

    // Database endpoint
    'endpoint'    => 'ydb.serverless.yandexcloud.net:2135',

    // Auto discovery (dedicated server only)
    'discovery'   => false,

    // IAM config
    'iam_config'  => [
        'temp_dir'       => './tmp', // Temp directory
        'root_cert_file' => './CA.pem', // Root CA file (dedicated server only!)

        // OAuth token authentication
        'oauth_token'    => 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA',
    ],
];

$ydb = new Ydb($config);

```

## JWT + private key

Create [a service account](https://cloud.yandex.ru/docs/iam/operations/sa/create) with the `editor` role, then create a private key. Also you need a key ID and a service account ID.

Connect to your database:

```php
<?php

use YandexCloud\Ydb\Ydb;

$config = [
    'database'    => '/ru-central1/b1glxxxxxxxxxxxxxxxx/etn0xxxxxxxxxxxxxxxx',
    'endpoint'    => 'ydb.serverless.yandexcloud.net:2135',
    'discovery'   => false,
    'iam_config'  => [
        'temp_dir'           => './tmp', // Temp directory
        'root_cert_file'     => './CA.pem', // Root CA file (dedicated server only!)

        // Private key authentication
        'key_id'             => 'ajexxxxxxxxx',
        'service_account_id' => 'ajeyyyyyyyyy',
        'private_key_file'   => './private.key',
    ],
];

$ydb = new Ydb($config);

```


## JWT + JSON file

Create [a service account](https://cloud.yandex.ru/docs/iam/operations/sa/create) with the `editor` role.

Create a service account [JSON file](https://cloud.yandex.ru/docs/iam/operations/iam-token/create-for-sa#via-cli), save it in your project as `sa_name.json`.

Connect to your database:

```php
<?php

use YandexCloud\Ydb\Ydb;

$config = [
    'database'    => '/ru-central1/b1glxxxxxxxxxxxxxxxx/etn0xxxxxxxxxxxxxxxx',
    'endpoint'    => 'ydb.serverless.yandexcloud.net:2135',
    'discovery'   => false,
    'iam_config'  => [
        'temp_dir'       => './tmp', // Temp directory
        'root_cert_file' => './CA.pem', // Root CA file (dedicated server only!)

        // Service account JSON file authentication
        'service_file'   => './sa_name.json',
    ],
];

$ydb = new Ydb($config);

```

## Metadata URL

When you deploy a project to VM or function at Yandex.Cloud, you are able to connect to the database using [Metadata URL](https://cloud.yandex.ru/docs/compute/operations/vm-connect/auth-inside-vm). Before you start, you should link your service account to an existing or new VM or function.

```php
<?php

use YandexCloud\Ydb\Ydb;

$config = [

    // Database path
    'database'    => '/ru-central1/b1glxxxxxxxxxxxxxxxx/etn0xxxxxxxxxxxxxxxx',

    // Database endpoint
    'endpoint'    => 'ydb.serverless.yandexcloud.net:2135',

    // Auto discovery (dedicated server only)
    'discovery'   => false,

    // IAM config
    'iam_config'  => [
        'temp_dir'     => './tmp', // Temp directory
        'use_metadata' => true,
    ],
];

$ydb = new Ydb($config);

```
## Anonymous

```php
<?php
use YandexCloud\Ydb\Ydb;
$config = [
    // Database path
    'database'    => '/local',
    // Database endpoint
    'endpoint'    => 'localhost:2135',
    // Auto discovery (dedicated server only)
    'discovery'   => false,
    // IAM config
    'iam_config'  => [
        'anonymous' => true,
        // Allow insecure grpc connection, default false
        'insecure' => false,
    ],
];
$ydb = new Ydb($config);
```

# Usage

You should initialize a session from the Table service to start querying.

```php
<?php

use YandexCloud\Ydb\Ydb;

$config = [
    // ...
];

$ydb = new Ydb($config);

// obtaining the Table service
$table = $ydb->table();

// obtaining a session
$session = $table->session();

// making a query
$result = $session->query('select * from `users` limit 10;');

$users_count = $result->rowCount();
$users = $result->rows();

$columns = $result->columns();

```

Also, you may call the `query()` method directly on the Table service. In this case a session will be created behind the scenes, and it will proxy your query to the session.

```php
<?php

$table = $ydb->table();

// making a query
$result = $table->query('select * from `users` limit 10;');

```

As soon as your script is finished, the session will be destroyed.

## Customizing queries

Normally, a regular query through the `query()` method is sufficient, but in exceptional cases, you may need to fine-tune the query settings. You could do it using the query builder:

```php
<?php

$session = $table->session();

// creating a new query builder instance
$query = $session->newQuery('select * from `users` limit 10;');

// a setting to keep in cache
$query->keepInCache();

// a setting to begin a transaction with the given mode
$query->beginTx('stale');

$result = $query->execute();
```

Methods of the query builder:

- `keepInCache(bool $value)` - keep in cache (default: `true`)
- `collectStats(int $value)` - collect stats (default: 1)
- `parameters(array $parameters)` - parameters
- `operationParams(\Ydb\Operations\OperationParams $operation_params)` - operation params
- `beginTx(string $mode)` - begin a transaction with the given [mode](https://cloud.yandex.ru/docs/ydb/concepts/transactions):
    - stale
    - online
    - online_inconsistent
    - serializable
- `txControl(\Ydb\Table\TransactionControl $tx_control)` - transaction control with custom settings

You can chain these methods for convenience.
