<?php

/**
 * Задача этого объекта - формировать результат работы LevelManager
 *
 * Можно было бы сделать ответ простым массивом, но реализация через объект лучше
 */
class Level
{
    /** @var int - Уровень */
    public $lvl;

    /** @var int - Общее количество опыта, необходимое для получения данного уровня */
    public $expTotal;

    /** @var int - Необходимое количество опыта до следующего уровня */
    public $expToLvl;

    /**
     * Заполняем объект данными
     *
     * @param int $lvl
     * @param int $expTotal
     * @param int $expToLvl
     */
    public function __construct(int $lvl, int $expTotal, int $expToLvl)
    {
        $this->lvl = $lvl;
        $this->expTotal = $expTotal;
        $this->expToLvl = $expToLvl;
    }
}
