<?php

/**
 * Данный класс работы с БД написан для примера.
 * Что здесь можно посмотреть - SQL запросы для тех или иных нужд
 * Но сам подход сделан по принципу "говнокод, лишь бы работало"
 *
 * Если же вы хотите знать, как правильно работать с БД - посмотрите
 * как это реализовано в таких популярных фрейморвках как: Yii2, Laravel, Symfony
 */

class Model
{
    public $DB;

    public function __construct()
    {
        $this->DB = mysqli_connect('localhost', 'user', 'password', 'database')
        or die('Невозможно подключиться к серверу БД. ' . mysql_error());
        $this->DB->query('SET NAMES utf8');
    }

    public function getUserInfo($id)
    {
        $queryString = 'SELECT users.name, users.lvl, users.exp 
                        FROM `users` 
                        WHERE users.id = ?';
        if ($stmt = $this->DB->prepare($queryString)) {
            $stmt->bind_param(
                "i",
                $id
            );
            $stmt->execute();
            if (!$stmt->error) {
                $userinfo = array();
                $stmt->bind_result(
                    $userinfo['name'],
                    $userinfo['lvl'],
                    $userinfo['exp']
                );
                $stmt->fetch();
            }
            $stmt->close();
        }

        return $userinfo;
    }

    public function getExpToLevel($id)
    {
        $queryString = 'SELECT `levels`.`exp_to_lvl` 
                    FROM levels 
                    JOIN users ON users.lvl = levels.lvl 
                    WHERE users.id = ?';
        if ($stmt = $this->DB->prepare($queryString)) {
            $stmt->bind_param(
                "i",
                $id
            );
            $stmt->execute();
            if (!$stmt->error) {
                $stmt->bind_result($expToLevel);
                $stmt->fetch();
            }
            $stmt->close();
        }

        return $expToLevel;
    }

    public function getExpAtLevel($totalexp, $lvl)
    {
        $queryString = 'SELECT ? - exp_total FROM levels WHERE lvl = ?';
        if ($stmt = $this->DB->prepare($queryString)) {
            $stmt->bind_param(
                "ii",
                $totalexp,
                $lvl
            );
            $stmt->execute();
            if (!$stmt->error){
                $stmt->bind_result($expAtLevel);
                $stmt->fetch();
            }
            $stmt->close();
        }

        return $expAtLevel;
    }

    public function getLevel($totalexp)
    {
        $queryString = 'SELECT MAX(t.lvl) FROM (SELECT lvl FROM levels WHERE exp_total - ? <= 0) t';
        if ($stmt = $this->DB->prepare($queryString)) {
            $stmt->bind_param(
                "i",
                $totalexp
            );
            $stmt->execute();
            if (!$stmt->error){
                $stmt->bind_result($level);
                $stmt->fetch();
            }
            $stmt->close();
        }

        return $level;
    }

    public function setLevel($id, $lvl)
    {
        $queryString = 'UPDATE users SET lvl = ? 
                          WHERE id = ?';
        if ($stmt = $this->DB->prepare($queryString)) {
            $stmt->bind_param(
                "ii",
                $lvl,
                $id
            );
            $stmt->execute();
            $stmt->close();
        }
    }

    public function editExp($id, $exp)
    {
        $queryString = 'UPDATE users SET exp = ? 
                          WHERE id = ?';
        if ($stmt = $this->DB->prepare($queryString)) {
            $stmt->bind_param(
                "ii",
                $exp,
                $id
            );
            $stmt->execute();
            $stmt->close();
        }
    }
}
