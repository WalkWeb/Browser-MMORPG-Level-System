<?php

class User extends Model
{
    /** @var int - ID игрока */
    public $id;

    /** @var string - Имя игрока */
    public $name;

    /** @var int - Уровень игрока */
    public $lvl;

    /** @var int - Опыт игрока */
    public $exp;

    /** @var LevelManager - Объект для работы с данными, относящихся к уровню */
    private $levelManager;

    /**
     * При создании модели пользователя сразу заполним её данными, на основе указанного ID пользователя
     *
     * @param int $id
     * @param LevelManager $levelManager
     */
    public function __construct(int $id, LevelManager $levelManager)
    {
        parent::__construct();
        $this->id = $id;
        $this->levelManager = $levelManager;
        $this->setParams();
    }

    /**
     * Обнуляет опыт пользователя
     */
    public function removeExp(): void
    {
        $this->lvl = 1;
        $this->exp = 0;
        $this->save();
    }

    /**
     * Возвращает количество опыта на текущем уровне
     *
     * @return int
     */
    public function getExpAtLvl(): int
    {
        return $this->exp - $this->levelManager->getLevel($this->lvl)->expTotal;
    }

    /**
     * Возвращает количество опыта, необходимое для получения нового уровня
     *
     * @return int
     */
    public function getExpToLvl(): int
    {
        return $this->levelManager->getLevel($this->lvl)->expToLvl;
    }

    /**
     * Возвращает длину полоски опыта полученного на данном уровне
     *
     * @return float
     */
    public function getExpWeight(): float
    {
        return round($this->getExpAtLvl()/$this->getExpToLvl() * 100, 1);
    }

    /**
     * Добавляет пользователю 5 опыта
     */
    public function addSmallExp(): void
    {
        $this->addExp(5);
    }

    /**
     * Добавляет пользователю 20 опыта
     */
    public function addMediumExp(): void
    {
        $this->addExp(20);
    }

    /**
     * Добавляет пользователю 50 опыта
     */
    public function addBigExp(): void
    {
        $this->addExp(50);
    }

    /**
     * Добавляет указанное количество опыта пользователю
     *
     * Мы моглибы сделать этот метод публичным, и передавать в него значения. Но, в этом случае нам было бы сложнее
     * контролировать корректность добавляемого опыта. Чтобы избежать этого, делаем три отдельных метода:
     *
     * addSmallExp()
     * addMediumExp()
     * addBigExp()
     *
     * Которые уже будут вызывать addExp() с фиксированным количеством опыта.
     *
     * @param int $exp
     */
    private function addExp(int $exp): void
    {
        // Но на всякий случай, мы делаем дополнительную проверку от дурака
        if ($exp < 1) {
            // В рабочем проекте здесь должно бросаться исключение
            die('Не может добавляться нулевой или отрицательный опыт');
        }

        $this->exp += $exp;

        /**
         * Если вам нужно сделать какие-нибудь действия при повышении уровня (например повысить здоровье) - делайте
         * её здесь - сравнивая старый уровень с новым, т.е.:
         *
         * $newLevel = $this->levelManager->getLevelByExp($this->exp)->lvl;
         *
         * if ($newLevel > $this->lvl) {
         *      // делаем, что нужно при повышении уровня
         * }
         *
         * $this->lvl = $newLevel;
         */

        $this->lvl = $this->levelManager->getLevelByExp($this->exp)->lvl;
        $this->save();
    }

    /**
     * Заполняет модель данными из базы
     */
    private function setParams(): void
    {
        $query = $this->db->query(
            'SELECT `name`, `lvl`, `exp` FROM `users` WHERE `id` = ?',
            [['type' => 'i', 'value' => $this->id]],
            true
        );

        if (!$query) {
            // В рабочем проекте здесь должно бросаться исключение
            die('Нет данных по персонажу');
        }

        $this->name = $query['name'];
        $this->lvl = $query['lvl'];
        $this->exp = $query['exp'];
    }

    /**
     * Сохраняет данные по уровню и опыту пользователя
     */
    private function save(): void
    {
        $this->db->query(
            'UPDATE `users` SET `lvl` = ?, `exp` = ? WHERE `id` = ?',
            [
                ['type' => 'i', 'value' => $this->lvl],
                ['type' => 'i', 'value' => $this->exp],
                ['type' => 'i', 'value' => $this->id],
            ]
        );
    }
}
