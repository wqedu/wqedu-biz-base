<?php

use Doctrine\DBAL\DriverManager;
use Phpmig\Adapter;
use Pimple\Container;
use Symfony\Component\Finder\Finder;

$container = new Container();

$container['db'] = function () {
    return DriverManager::getConnection(array(
        'dbname' => getenv('DB_NAME') ? : 'wqedu_biz_course_test',
        'user' => getenv('DB_USER') ? : 'root',
        'password' => getenv('DB_PASSWORD') ? : '123456',
        'host' => getenv('DB_HOST') ? : '127.0.0.1',
        'port' => getenv('DB_PORT') ? : 3306,
        'driver' => 'pdo_mysql',
        'charset' => 'utf8',
    ));
};

$container['phpmig.adapter'] = function ($c) {
    return new Adapter\Doctrine\DBAL($c['db'], 'migrations');
};

$container['phpmig.migrations_path'] = __DIR__ . '/migrations';

$container['db']->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

return $container;