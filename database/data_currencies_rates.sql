--
-- Структура таблицы `data_currencies_rates`
--

CREATE TABLE `data_currencies_rates` (
    `rate_id` int(11) UNSIGNED NOT NULL COMMENT 'ID инкремента справочника',
    `rate_currency_base` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Код базовой валюты',
    `rate_currency_target` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Код целевой валюты',
    `rate_date` date NOT NULL COMMENT 'Дата значения обменного курса',
    `rate_value` float NOT NULL COMMENT 'Значение обменного курса'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Дамп данных таблицы `data_currencies_rates`
--

INSERT INTO `data_currencies_rates` (`rate_id`, `rate_currency_base`, `rate_currency_target`, `rate_date`, `rate_value`) VALUES
    (1, 'CHF', 'EUR', '2022-06-13', 0.963279),
    (2, 'CHF', 'EUR', '2022-06-08', 0.953192),
    (3, 'CHF', 'EUR', '2022-06-06', 0.962928),
    (4, 'CHF', 'EUR', '2022-06-03', 0.969613),
    (5, 'CHF', 'EUR', '2022-05-31', 0.970843),
    (6, 'CHF', 'EUR', '2022-05-27', 0.972468),
    (7, 'CHF', 'EUR', '2022-05-25', 0.973144),
    (8, 'CHF', 'EUR', '2022-05-20', 0.971084),
    (9, 'CHF', 'EUR', '2022-05-18', 0.967309),
    (10, 'CHF', 'EUR', '2022-05-14', 0.958987),
    (11, 'CHF', 'EUR', '2022-05-11', 0.956502),
    (12, 'CHF', 'EUR', '2022-05-10', 0.953553),
    (13, 'CHF', 'EUR', '2022-05-06', 0.958657),
    (14, 'CHF', 'EUR', '2022-05-02', 0.973092),
    (15, 'CHF', 'EUR', '2022-04-29', 0.973312),
    (16, 'CHF', 'EUR', '2022-04-27', 0.978023),
    (17, 'CHF', 'EUR', '2022-04-25', 0.973919),
    (18, 'CHF', 'EUR', '2022-04-21', 0.967706),
    (19, 'CHF', 'EUR', '2022-04-17', 0.981141),
    (20, 'CHF', 'EUR', '2022-04-13', 0.982675),
    (21, 'CHF', 'EUR', '2022-04-08', 0.984181),
    (22, 'CHF', 'EUR', '2022-04-06', 0.983363),
    (23, 'CHF', 'EUR', '2022-04-04', 0.983611),
    (24, 'CHF', 'EUR', '2022-04-01', 0.977803),
    (25, 'CHF', 'EUR', '2022-03-30', 0.970493),
    (26, 'CHF', 'EUR', '2022-03-28', 0.9742),
    (27, 'CHF', 'EUR', '2022-03-24', 0.977182),
    (28, 'CHF', 'EUR', '2022-03-22', 0.971214),
    (29, 'CHF', 'EUR', '2022-03-18', 0.969874),
    (30, 'CHF', 'EUR', '2022-03-16', 0.963535),
    (31, 'CHF', 'EUR', '2022-03-14', 0.973636),
    (32, 'CHF', 'EUR', '2022-03-09', 0.974845),
    (33, 'CHF', 'EUR', '2022-03-08', 0.987035),
    (34, 'CHF', 'EUR', '2022-03-06', 0.999696),
    (35, 'CHF', 'EUR', '2022-03-03', 0.984313),
    (36, 'CHF', 'EUR', '2022-03-02', 0.977598),
    (37, 'CHF', 'EUR', '2022-02-24', 0.965235),
    (38, 'CHF', 'EUR', '2022-02-09', 0.947209),
    (39, 'CHF', 'EUR', '2022-02-05', 0.944218),
    (41, 'CHF', 'EUR', '2022-06-10', 0.962493),
    (42, 'CHF', 'EUR', '2022-06-09', 0.960353),
    (44, 'CHF', 'EUR', '2022-06-07', 0.960186),
    (46, 'CHF', 'EUR', '2022-06-05', 0.968841),
    (48, 'CHF', 'EUR', '2022-06-02', 0.971417),
    (49, 'CHF', 'EUR', '2022-06-01', 0.974512),
    (51, 'CHF', 'EUR', '2022-05-30', 0.968642),
    (53, 'CHF', 'EUR', '2022-05-26', 0.971955),
    (55, 'CHF', 'EUR', '2022-05-24', 0.969963),
    (56, 'CHF', 'EUR', '2022-05-23', 0.969115),
    (58, 'CHF', 'EUR', '2022-05-19', 0.972251),
    (60, 'CHF', 'EUR', '2022-05-17', 0.953822),
    (61, 'CHF', 'EUR', '2022-05-16', 0.956139),
    (62, 'CHF', 'EUR', '2022-05-13', 0.958842),
    (63, 'CHF', 'EUR', '2022-05-12', 0.960334),
    (66, 'CHF', 'EUR', '2022-05-09', 0.953251),
    (125, 'CHF', 'EUR', '2022-05-05', 0.962876),
    (126, 'CHF', 'EUR', '2022-05-04', 0.967391),
    (127, 'CHF', 'EUR', '2022-05-03', 0.970494),
    (129, 'CHF', 'EUR', '2022-04-30', 0.974031),
    (131, 'CHF', 'EUR', '2022-04-28', 0.979678),
    (133, 'CHF', 'EUR', '2022-04-26', 0.976379),
    (135, 'CHF', 'EUR', '2022-04-23', 0.967091),
    (136, 'CHF', 'EUR', '2022-04-22', 0.966978),
    (138, 'CHF', 'EUR', '2022-04-20', 0.97103),
    (139, 'CHF', 'EUR', '2022-04-19', 0.973003),
    (140, 'CHF', 'EUR', '2022-04-14', 0.979907),
    (142, 'CHF', 'EUR', '2022-04-12', 0.990383),
    (143, 'CHF', 'EUR', '2022-04-11', 0.986925),
    (145, 'CHF', 'EUR', '2022-04-07', 0.984897),
    (146, 'CHF', 'EUR', '2022-04-05', 0.986258),
    (148, 'CHF', 'EUR', '2022-04-03', 0.977593),
    (149, 'CHF', 'EUR', '2022-04-02', 0.977698),
    (151, 'CHF', 'EUR', '2022-03-31', 0.978272),
    (154, 'CHF', 'EUR', '2022-03-25', 0.979594),
    (156, 'CHF', 'EUR', '2022-03-23', 0.976019),
    (158, 'CHF', 'EUR', '2022-03-21', 0.971869),
    (160, 'CHF', 'EUR', '2022-03-17', 0.96242),
    (162, 'CHF', 'EUR', '2022-03-15', 0.968996),
    (164, 'CHF', 'EUR', '2022-03-11', 0.977627),
    (165, 'CHF', 'EUR', '2022-03-10', 0.976879),
    (168, 'CHF', 'EUR', '2022-03-07', 0.994462),
    (169, 'CHF', 'EUR', '2022-03-05', 0.996812),
    (170, 'CHF', 'EUR', '2022-03-04', 0.996734),
    (173, 'CHF', 'EUR', '2022-02-23', 0.963856),
    (174, 'CHF', 'EUR', '2022-02-06', 0.943582),
    (175, 'CHF', 'EUR', '2022-02-03', 0.94998),
    (176, 'CHF', 'EUR', '2022-02-02', 0.962948),
    (177, 'CHF', 'EUR', '2025-07-02', 1.07);


--
-- Индексы таблицы `data_currencies_rates`
--
ALTER TABLE `data_currencies_rates`
    ADD PRIMARY KEY (`rate_id`),
    ADD UNIQUE KEY `rate_currency_base` (`rate_currency_base`,`rate_currency_target`,`rate_date`),
    ADD KEY `rate_currency_target` (`rate_currency_target`);


--
-- AUTO_INCREMENT для таблицы `data_currencies_rates`
--
ALTER TABLE `data_currencies_rates`
    MODIFY `rate_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID инкремента справочника', AUTO_INCREMENT=178;


--
-- Ограничения внешнего ключа таблицы `data_currencies_rates`
--
ALTER TABLE `data_currencies_rates`
    ADD CONSTRAINT `data_currencies_rates_ibfk_1` FOREIGN KEY (`rate_currency_base`) REFERENCES `swa_luchkinpro_demo_system`.`system_currencies` (`currency_code`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `data_currencies_rates_ibfk_2` FOREIGN KEY (`rate_currency_target`) REFERENCES `swa_luchkinpro_demo_system`.`system_currencies` (`currency_code`) ON DELETE CASCADE ON UPDATE CASCADE;
