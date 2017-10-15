<?php

include_once('model.php');

class Level
{
    /**
     * ID пользователя
     */
    public $id = 1;

    /**
     * Информация о пользователе:
     * $userinfo[name] - имя
     * $userinfo[lvl] - уровень
     * $userinfo[exp] - общее количество опыта
     * $userinfo[exp_to_lvl] - опыта до следующего уровня
     * $userinfo[exp_at_lvl] - опыта на текущем уровне
     * $userinfo[expbar] - ширина бара опыта
     */
    public $userinfo;

    /**
     * Модель работы с базой данных
     */
    public $model;

    // Создает модель работы с бд и сразу получаем информацию о пользователе
    public function __construct()
    {
        $this->model = new Model();
        $this->getUserInfo();
    }

    public function getUserInfo()
    {
        $this->userinfo = $this->model->getUserInfo($this->id);
        $this->userinfo['exp_to_lvl'] = $this->model->getExpToLevel($this->id);
        $this->userinfo['exp_at_lvl'] = $this->model->getExpAtLevel($this->userinfo['exp'], $this->userinfo['lvl']);
        $this->userinfo['expbar'] = round(($this->userinfo['exp_at_lvl']/$this->userinfo['exp_to_lvl']) * 100);
    }

    // Обновляем и возвращаем свежую информацию о пользователе
    public function getNewUserInfo()
    {
        $this->model = new Model();
        $this->getUserInfo();
        return $this->userinfo;
    }

    // Добавление опыта
    public function addExp($exp)
    {
        // Добавляем новый опыт (считаем, что он может только увеличиться)
        $this->userinfo['exp'] += $exp;
        $this->model->editExp($this->id, $this->userinfo['exp']);

        // Получаем новый уровень на основе изменившегося опыта
        $newLevel = $this->model->getLevel($this->userinfo['exp']);

        // Проверяем, оказался ли новый уровень больше старого. Если да - записываем его в бд.
        // А также по необходимости, делаем необходимые события для нового уровня (например, добавляем здоровье)
        if ($newLevel > $this->userinfo['lvl']) {
            $this->model->setLevel($this->id, $newLevel);
        }
    }

    // Обнуление опыта
    public function zeroExp()
    {
        $this->userinfo['exp'] = 0;
        $this->model->editExp($this->id, $this->userinfo['exp']);

        // Получаем новый уровень на основе изменившегося опыта
        $newLevel = $this->model->getLevel($this->userinfo['exp']);

        // Если уровень стал ниже - изменяем его в бд
        if ($newLevel < $this->userinfo['lvl']) {
            $this->model->setLevel($this->id, $newLevel);
        }
    }
}
$page = new Level();

if ($_POST) {
    if ($_POST['exp'] === 'zero') {
        $page->zeroExp();
    } else {
        $page->addExp($_POST['exp']);
    }
}

$user = $page->getNewUserInfo();

?>
<html>
<head>
    <title>Пример системы уровней в браузерной MMORPG</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <style>
        * {margin: 0;padding: 0;}
        body {background: #2b2b2b;color: #f1f1f1;}
        .user {width: 250px; background: #3b3b3b; margin-left: auto; margin-right: auto; margin-top: 100px;}
        .ava {width: 250px; height: 250px; background: url('img/ava.jpg');}
        .name {width: 100%; padding: 3px; font-size: 22px; text-align: center;}
        .level {width: 100%; padding: 3px; font-size: 18px; text-align: center;}
        .exp {width: 100%; padding: 3px; font-size: 18px; text-align: center;}
        .expbar {width: 100%; height: 20px; background: #800;}
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
    <div class="name">Пользователь: <?= $user['name'] ?></div>
    <div class="level">Уровень: <?= $user['lvl'] ?></div>
    <div class="exp">Всего опыта: <?= $user['exp'] ?></div>
    <div class="expbar">
        <div class="expcur" style="width: <?= $user['expbar'] ?>%;"></div>
    </div>
    <div class="exptext">
        <?= $user['exp_at_lvl'] ?> / <?= $user['exp_to_lvl'] ?>
    </div>
</div>
<div class="formcont">
    <form method="post" action="">
        <input type="hidden" name="exp" value="5">
        <button type="submit">Добавить 5 опыта</button>
    </form>
    <form method="post" action="">
        <input type="hidden" name="exp" value="20">
        <button type="submit">Добавить 20 опыта</button>
    </form>
    <form method="post" action="">
        <input type="hidden" name="exp" value="50">
        <button type="submit">Добавить 50 опыта</button>
    </form>
    <form method="post" action="">
        <input type="hidden" name="exp" value="zero">
        <button type="submit">Обнулить опыт</button>
    </form>
</div>
</body>
</html>
