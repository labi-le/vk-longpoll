# vk-longpoll

[![GitHub license](https://img.shields.io/badge/license-BSD-green.svg)](https://github.com/labi-le/vk-longpoll-component/blob/main/LICENSE)
[![Packagist Stars](https://img.shields.io/packagist/stars/labile/vk-longpoll-component)](https://packagist.org/packages/labile/vk-longpoll/stats)
[![Packagist Stats](https://img.shields.io/packagist/dt/labile/vk-longpoll-component)](https://packagist.org/packages/labile/vk-longpoll/stats)


## Установка

`composer require labile/vk-longpoll`

### Реализация longpoll ВК на php

```php
<?php

use Astaroth\LongPoll\LongPoll;

const GROUP_ID = 1337;
const ACCESS_TOKEN = 1337;

$longpoll = new LongPoll(GROUP_ID);
$longpoll->setDefaultToken(ACCESS_TOKEN)
$longpoll->listen(static function($data){
//....
});

```

