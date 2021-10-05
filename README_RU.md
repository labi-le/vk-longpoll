# vk-longpoll

[![GitHub license](https://img.shields.io/badge/license-BSD-green.svg)](https://github.com/labi-le/vk-longpoll-component/blob/main/LICENSE)
[![Packagist Stars](https://img.shields.io/packagist/stars/labile/vk-longpoll)](https://packagist.org/packages/labile/vk-longpoll/stats)
[![Packagist Stats](https://img.shields.io/packagist/dt/labile/vk-longpoll)](https://packagist.org/packages/labile/vk-longpoll/stats)


## Установка

`composer require labile/vk-longpoll`

### Реализация longpoll ВК на php

```php
<?php

use Astaroth\Longpoll\Longpoll;

const ACCESS_TOKEN = "saassasasassssa";
const VK_VERSION = "5.131";

$longpoll = new Longpoll(ACCESS_TOKEN, VK_VERSION);
$longpoll->setWait(30);
$longpoll->listen(static function($data){
//....
});

```

