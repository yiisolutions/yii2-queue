# Yii2 Queue

Extension for work with queues.

## Installation

Use composer

```
composer require "yiisolutions/yii2-migrations-advanced: *"
```

or add to composer.json require section:

```
"yiisolutions/yii2-migrations-advanced": "*"
```

## Configuration

First, we add to the configuration component.

```php
<?php

return [
    // ...
    'components' => [
        // ...
        'queue' => [
            'class' => 'yisolutions\queue\Queue',
            'host' => 'localhost',
            'port' => 5672,
            'user' => 'guest',
            'password' => 'guest',
            'vhost' => '/',
        ],
        // ...
    ],
    // ...
];
```

## Send data

Next, to send data to the queue, we use the send() component method.

```php
<?php

use yiisolutions\queue\Queue;

// ...
$data = ['foo' => 'bar'];

/** @var Queue $queue */
$queue = Yii::$app->get('queue');
$queue->send('queue.name', $data);
```

## Receive data

We use the listen method to retrieve data.

```php
<?php

use yiisolutions\queue\Queue;
use PhpAmqpLib\Message\AMQPMessage;

// ...
/** @var Queue $queue */
$queue = Yii::$app->get('queue');
$queue->listen('queue.name', function(AMQPMessage $msg) use ($queue) {
    // process data
    
    // acknowledgment message
    $queue->acknowledgmentMessage($msg);
});
```
