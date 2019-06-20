<?php

/**
 * Напомню, что главная задача данного примера - показать новичкам в PHP как можно реализовать систему уровней.
 *
 * Для простоты примера не используется Composer, автозагрузка классов, и код стараюсь писать попроще
 */

// Параметры подключения к базе
define('DB_HOST', 'localhost');
define('DB_NAME', 'YOU_DATABASE_NAME');
define('DB_USER', 'YOU_DATABASE_USER');
define('DB_PASSWORD', 'YOU_DATABASE_PASSWORD');

// Сразу делаем проверку, если пользователь забыл заменить параметры подключения к базе на свои
if (DB_NAME === 'YOU_DATABASE_NAME' || DB_USER === 'YOU_DATABASE_USER' || DB_PASSWORD === 'YOU_DATABASE_PASSWORD') {
    die('<h1>Вы забыли указать свои параметры подключения к MySQL базе в index.php</h1>');
}

// Объект работы с базой
require_once 'src/DBConnection.php';

// Базовая модель
require_once 'src/Model.php';

// Модель пользователя
require_once 'Models/User.php';

// Модель уровня
require_once 'Models/LevelManager.php';

// Объект содержащий в себе данные по уровню
require_once 'Models/Level.php';

// Создаем объект менеджера уровней - он будет выдавать нам данные по уровню, на основе опыта или указанного уровня
$levelManager = new LevelManager();

// Создаем объект пользователя, передавая ему ID пользователя и менеджер уровней
$user = new User(1, $levelManager);

if (!empty($_POST['exp'])) {
    switch ($_POST['exp']) {
        case 'zero':
            $user->removeExp();
            break;
        case 'addSmallExp':
            $user->addSmallExp();
            break;
        case 'addMediumExp':
            $user->addMediumExp();
            break;
        case 'addBigExp':
            $user->addBigExp();
            break;
    }
}
?>
<html>
<head>
    <title>Пример системы уровней в браузерной MMORPG</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <style>
        * {margin: 0; padding: 0;}
        body {background: #2b2b2b; color: #f1f1f1;}
        .user {width: 250px; background: #3b3b3b; margin-left: auto; margin-right: auto; margin-top: 100px;}
        .ava {width: 250px; height: 250px; background: url('img/ava.jpg');}
        .name {width: 100%; padding: 3px; font-size: 22px; text-align: center;}
        .level {width: 100%; padding: 3px; font-size: 18px; text-align: center;}
        .exp {width: 100%; padding: 3px; font-size: 18px; text-align: center;}
        .expbar {width: 100%; height: 20px; background: #800; overflow: hidden;}
        .expcur {height: 20px; background: #f00; border-radius: 3px;}
        .exptext {width: 100%; height: 20px; font-size: 18px; text-align: center; margin-top: -20px;}
        button {background-color: #f44336;border: none;color: white;padding: 10px 22px;text-align: center;text-decoration: none;  display: inline-block;  font-size: 16px;  margin: 5px;}
        button:hover {background-color: #d7382c;  cursor: pointer;}
        form {float: left;}
        .formcont {width: 760px; margin-top: 100px; margin-left: auto; margin-right: auto;}
    </style>
</head>
<body>
<div class="user">
    <div class="ava"></div>
    <div class="name">Пользователь: <?= $user->name ?></div>
    <div class="level">Уровень: <?= $user->lvl ?></div>
    <div class="exp">Всего опыта: <?= $user->exp ?></div>
    <div class="expbar">
        <div class="expcur" style="width: <?= $user->getExpWeight() ?>%;"></div>
    </div>
    <div class="exptext">
        <?= $user->getExpAtLvl() ?> / <?= $user->getExpToLvl() ?>
    </div>
</div>
<div class="formcont">
    <form method="post" action="">
        <input type="hidden" name="exp" value="addSmallExp">
        <button type="submit">Добавить 5 опыта</button>
    </form>
    <form method="post" action="">
        <input type="hidden" name="exp" value="addMediumExp">
        <button type="submit">Добавить 20 опыта</button>
    </form>
    <form method="post" action="">
        <input type="hidden" name="exp" value="addBigExp">
        <button type="submit">Добавить 50 опыта</button>
    </form>
    <form method="post" action="">
        <input type="hidden" name="exp" value="zero">
        <button type="submit">Обнулить опыт</button>
    </form>
</div>
</body>
</html>