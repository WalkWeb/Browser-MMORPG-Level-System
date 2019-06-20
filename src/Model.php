<?php

/**
 * Базовая модель нашего проекта
 *
 * Также как и все остальное - крайне упрощенная, чтобы не перегружать пример кодом
 */
abstract class Model
{
    protected $db;

    public function __construct()
    {
        $this->db = DBConnection::getInstance();
    }
}
