<?php

/**
 * Генератор таблицы уровней
 *
 * Для максимальной простоты - здесь все на простых функциях (точнее на одной функции).
 */

// Стартовый уровень
$lvl = 1;

// Стартовый опыт
$totalExp = 0;

// До какого уровня делать расчет
$maxLvl = 10;

function generate(int $lvl, int $totalExp, int $maxLvl): array
{
    $html = '';
    $sql = '';

    $html .= '<table border="1"><tr><td>Уровень</td><td>Всего опыта</td><td>Опыта до<br />следующего уровня</td></tr>';
    $sql .= 'DELETE FROM `levels`; INSERT INTO `levels` (`lvl`, `exp_total`, `exp_to_lvl`) VALUES <br />';

    while ($lvl <= $maxLvl) {

        $expToLvl = round(($lvl * 10) ** 1.7); // Сама формула прогресса требуемого опыта

        $html .= '<tr><td>' . $lvl . '</td><td>' . $totalExp . '</td><td>' . $expToLvl . '</td></tr>';
        $sql .= '(' . $lvl . ', ' . $totalExp . ', ' . $expToLvl . '),<br />';

        $totalExp += $expToLvl;
        $lvl++;
    }

    $html .= '</table>';
    $sql = substr($sql, 0, -7);
    $sql .= ';';

    return ['html' => $html, 'sql' => $sql];
}

$generate = generate($lvl, $totalExp, $maxLvl);

?>
<html>
<head>
    <title>Генератор таблицы уровней</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
</head>
<body>

<h1>Генератор таблицы уровней</h1>

<p>
    Механика роста уровня (в зависимости от количества опыта) может меняться. Чтобы легко вносить изменения в проект
    &#151; сделан генератор таблицы уровней. Укажите необходимую формулу расчета уровня &#151; и скрипт сам рассчитает
    всю таблицу целиком, и подготовит SQL-запрос для обновления данных.
</p>

<?= $generate['html'] ?>

<br /><br />
<h3>Просто выполните указанный ниже SQL-код, чтобы обновить данные по уровням:</h3>

<?= $generate['sql'] ?>
