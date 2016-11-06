[Демо](http://139.59.128.4/)

##Использовал: 

- Ubuntu 16.04.1 LTS 
- nginx 1.10.0 
- MySql 5.7.15 
- PHP 7.0 
- Composer 1.2.2 
- Redis 3.2.5 
- Node v6.9.1 
- npm 3.10.8 

##Развертывание: 

Cоздать файл конфигурации окружения `env.php`
```
$ cp env-example.php env.php
```

Отредактировать `env.php` в соответствии с текущими настройками системы

Установить PHP зависимости
```
$ composer install 
```

Установить зависимости Node.js
```
$ npm install
```

Мигрировать схему БД
```
$ php cli migrations:migrate
```

Собрать статику
```
$ npm run prod
```
