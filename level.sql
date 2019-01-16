




-- Таблица Users

CREATE TABLE `users` (
	`id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	`name` VARCHAR(255),
	`lvl`INT DEFAULT 1,
	`exp` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Таблица Levels

CREATE TABLE `levels` (
	`lvl` INT UNSIGNED DEFAULT NULL,
	`exp_to_lvl` INT DEFAULT NULL,
	`exp_total` INT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Заполняем таблицу User

INSERT INTO `users` (`id`, `name`, `lvl`, `exp`)
VALUES
(NULL, 'Diablo', '1', '0');

-- Заполняем таблицу Levels

INSERT INTO `levels` (`lvl`, `exp_total`, `exp_to_lvl`)
VALUES
('1', '0', '25'),
('2', '25', '66'),
('3', '91', '117'),
('4', '208', '175'),
('5', '383', '239'),
('6', '622', '309'),
('7', '931', '383'),
('8', '1314', '462'),
('9', '1776', '544'),
('10', '2320', '1000000');


-- Удаление таблиц

DROP TABLE `users`;
DROP TABLE `levels`;
