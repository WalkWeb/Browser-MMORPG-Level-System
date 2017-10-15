<html>
<head>
    <title>Создаем таблицу уровней и требуемого опыта</title>
</head>
<body>

<?php

$lvl = 1;
$a = 1.4;
$totalexp = 0;

echo '<table border="1"><tr><td>Уровень</td><td>Всего опыта</td><td>Опыта до<br />следующего уровня</td></tr>';
while ($lvl < 11) {
    $y = round(pow(($lvl * 10), $a));
    echo '<tr><td>'.$lvl.'</td><td>'.$totalexp.'</td><td>'.$y.'</td></tr>';
    $totalexp += $y;
    $lvl++;
}
echo '</table>';


