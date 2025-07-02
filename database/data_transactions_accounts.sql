--
-- Структура таблицы `data_transactions_accounts`
--

CREATE TABLE `data_transactions_accounts` (
    `account_id` int(11) UNSIGNED NOT NULL COMMENT 'ID инкремент',
    `account_name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Название аккаунта',
    `account_currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Код валюты счета',
    `account_balance_start` float NOT NULL DEFAULT 0 COMMENT 'Начальное значение баланса в валюте счета',
    `account_balance_end` float NOT NULL DEFAULT 0 COMMENT 'Рассчитанное значение итогового баланса в валюте счета',
    `account_balance_end_chf` float NOT NULL DEFAULT 0 COMMENT 'Рассчитанное значение итогового баланса в CHF'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `data_transactions_accounts`
--

INSERT INTO `data_transactions_accounts` (`account_id`, `account_name`, `account_currency`, `account_balance_start`, `account_balance_end`, `account_balance_end_chf`) VALUES
    (634, 'Revolut', 'EUR', 0, 2271.56, 0),
    (635, 'Stripe', 'CHF', 0, -2757.93, 0);


--
-- Индексы таблицы `data_transactions_accounts`
--
ALTER TABLE `data_transactions_accounts`
    ADD PRIMARY KEY (`account_id`),
    ADD UNIQUE KEY `account_name` (`account_name`,`account_currency`),
    ADD KEY `account_currency` (`account_currency`);


--
-- AUTO_INCREMENT для таблицы `data_transactions_accounts`
--
ALTER TABLE `data_transactions_accounts`
    MODIFY `account_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID инкремент', AUTO_INCREMENT=636;


--
-- Ограничения внешнего ключа таблицы `data_transactions_accounts`
--
ALTER TABLE `data_transactions_accounts`
    ADD CONSTRAINT `data_transactions_accounts_ibfk_1` FOREIGN KEY (`account_currency`) REFERENCES `swa_luchkinpro_demo_system`.`system_currencies` (`currency_code`) ON DELETE CASCADE ON UPDATE CASCADE;
