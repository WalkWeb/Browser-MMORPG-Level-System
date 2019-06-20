<?php

/**
 * Объект для работы с базой
 *
 * Также как и все остальное - крайне упрощенный, чтобы не усложнять пример
 */
class DBConnection
{
    /** @var \mysqli */
    private $conn;

    /**@var string ошибки */
    private $error = '';

    /** @var DBConnection */
    private static $instance;

    /**
     * Объект работы с базой получаем по паттерну Singleton, чтобы не создавать отдельное подключение к базе на каждый
     * SQL-запрос.
     *
     * @return DBConnection
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Основной метод для обработки запроса, с валидацией параметров и, соответственно, с защитой от SQL-инъекций
     *
     * @param $sql
     * @param array $params
     * @param bool $single
     * @return array
     */
    public function query($sql, $params = [], $single = false): array
    {
        if ($single) {
            $sql .= ' LIMIT 1';
        }

        $param_arr = null;

        if (count($params) > 0) {
            $param_types = '';
            $param_arr = [0 => ''];
            foreach ($params as $key => $val) {
                $param_types .= $val['type'];
                $param_arr[] = &$params[$key]['value']; // Передача значений осуществляется по ссылке.
            }
            $param_arr[0] = &$param_types;
        }

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            $this->error = 'Ошибка подготовки SQL: ' . $this->conn->errno . ' ' . $this->conn->error . '. SQL: ' . $sql;
        } else {
            // Если параметры не пришли - то bind_param не требуется
            if (count($params) > 0) {
                call_user_func_array([$stmt, 'bind_param'], $param_arr);
            }
            if ($stmt->execute()) {
                $res = $stmt->get_result();
                if ($res !== false) {
                    $result = [];
                    $i = 0;
                    while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
                        $result[] = $row;
                        $i++;
                    }
                    if ($single && ($i === 1)) {
                        $result = $result[0];
                    }
                }
            } else {
                $this->error = 'Ошибка выполнения SQL: ' . $stmt->errno . ' ' . $stmt->error . '. SQL: ' . $sql;
            }
        }

        if (!$this->success()) {
            // Конечно, в рабочем проекте здесь должно бросаться исключение
            die($this->getError());
        }

        return $result ?? [];
    }

    /**
     * Само подключение
     *
     * Connection constructor.
     */
    private function __construct()
    {
        $this->conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
        or die('Невозможно подключиться к MySQL');

        // Проверка соединения
        if (mysqli_connect_errno()) {
            $this->error = 'Соединение не установлено: ' . mysqli_connect_error();
        } else {
            $this->conn->query('SET NAMES utf8');
            $this->conn->set_charset('utf8');
        }
    }

    /**
     * Закрывает соединение с бд
     */
    public function __destruct()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    /**
     * Возвращает true если все ок, и false - если есть ошибки
     *
     * @return bool
     */
    public function success(): bool
    {
        $error = $this->getError();
        return ($error === '' || $error === null);
    }

    /**
     * Возвращает ошибку
     *
     * @return string
     */
    public function getError(): string
    {
        if ($this->error) {
            return $this->error;
        }

        return $this->conn->error;
    }

    /**
     * Возвращает ID добавленной записи
     *
     * @return int|string
     */
    public function insertId()
    {
        return mysqli_insert_id($this->conn);
    }
}
