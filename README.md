# vk-utils

[![GitHub license](https://img.shields.io/badge/license-BSD-green.svg)](https://github.com/labi-le/vk-longpoll-component/blob/main/LICENSE)
[![Packagist Stars](https://img.shields.io/packagist/stars/labile/vk-longpoll-component)](https://packagist.org/packages/labile/vk-longpoll-component/stats)
[![Packagist Stats](https://img.shields.io/packagist/dt/labile/vk-longpoll-component)](https://packagist.org/packages/labile/vk-longpoll-component/stats)

[Документация на русском языке](https://github.com/labi-le/vk-longpoll-component/blob/main/README_RU.md)

## Installation

`composer require labile/vk-longpoll-component`

### Implementation of long polling VK in php

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

