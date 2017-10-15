




-- Таблица Users

CREATE TABLE `users` (
	`id` INT AUTO_INCREMENT PRIMARY KEY,
	`name` VARCHAR(255),
	`lvl`INT DEFAULT 1,
	`exp` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Таблица Levels

CREATE TABLE `levels` (
	`id` INT AUTO_INCREMENT PRIMARY KEY,
	`lvl` INT DEFAULT NULL,
	`exp_to_lvl` INT DEFAULT NULL,
	`exp_total` INT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Заполняем таблицу User

INSERT INTO `users` (`id`, `name`, `lvl`, `exp`)
VALUES
(NULL, 'Diablo', '1', '0');

-- Заполняем таблицу Levels

INSERT INTO `levels` (`id`, `lvl`, `exp_total`, `exp_to_lvl`)
VALUES
(NULL, '1', '0', '25'),
(NULL, '2', '25', '66'),
(NULL, '3', '91', '117'),
(NULL, '4', '208', '175'),
(NULL, '5', '383', '239'),
(NULL, '6', '622', '309'),
(NULL, '7', '931', '383'),
(NULL, '8', '1314', '462'),
(NULL, '9', '1776', '544'),
(NULL, '10', '2320', '1000000');


-- Удаление таблиц

DROP TABLE `users`;
DROP TABLE `levels`;