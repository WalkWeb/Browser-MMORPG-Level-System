<?php

class LevelManager extends Model
{
    /** @var Level - чтобы при каждом запросе не делать отдельный SQL-запрос, мы сохраняем результат */
    private $level;

    /**
     * Возвращает информацию по уровню (упакованную в виде объекта Level) по указанному уровню
     *
     * Внимательный программист задастся вопросом «Зачем мы делаем дополнительный SQL-запрос, для простого отображения
     * данных о пользователе?» - действительно, можно хранить данные exp_to_lvl и exp_total в самой таблице
     * пользователя, или, если не хочется «раздувать» таблицу параметрами, которые там можно и не хранить - сделать,
     * допустим, статическое свойство $levelMap = [1=> [...], 2 => [...]], в котором уже хранить данные по уровням. И
     * простым запросом к этому свойству-массиву получать данные по уровню. Правда, придется следить за тем, чтобы
     * данные в MySQL-базе и php-файле были идентичными.
     *
     * @param int $lvl
     * @return Level
     */
    public function getLevel(int $lvl): Level
    {
        // Если мы уже получали запрос по уровню, и этот уровень не изменился - возвращаем его
        if ($this->level !== null && $lvl === $this->level->lvl) {
            return $this->level;
        }

        // Иначе, делаем запрос на получение данных по уровню
        $query = $this->db->query(
            'SELECT `exp_to_lvl`, `exp_total` FROM `levels` WHERE `lvl` = ?',
            [['type' => 'i', 'value' => $lvl]],
            true
        );

        if (!$query) {
            // В рабочем проекте здесь должно бросаться исключение
            die('Не могу получить данные по уровню');
        }

        return $this->level = new Level($lvl, $query['exp_total'], $query['exp_to_lvl']);
    }

    /**
     * Возвращает информацию по уровню (упакованную в виде объекта Level) на основе указанного опыта
     *
     * Этот запрос будет выполняться только при изменении опыта пользователя
     *
     * @param int $exp
     * @return Level
     */
    public function getLevelByExp(int $exp): Level
    {
        $query = $this->db->query(
            'SELECT 

                MAX(`t`.`lvl`) as `lvl`,
                MAX(`t`.`exp_to_lvl`) as `exp_to_lvl`,
                MAX(`t`.`exp_total`) as `exp_total`
                
                FROM (SELECT `lvl`, `exp_to_lvl`, `exp_total` FROM `levels` WHERE `exp_total` - ? <= 0) as `t`',
            [['type' => 'i', 'value' => $exp]],
            true
        );

        if (!$query) {
            // В рабочем проекте здесь должно бросаться исключение
            die('Не могу получить данные по уровню');
        }

        return $this->level = new Level($query['lvl'], $query['exp_total'], $query['exp_to_lvl']);
    }
}
