# Robokassa payment gateway plugin for Moodle.

[![](https://img.shields.io/github/v/release/Snickser/moodle-paygw_robokassa.svg)](https://github.com/Snickser/moodle-paygw_robokassa/releases)
[![Build Status](https://github.com/Snickser/moodle-paygw_robokassa/actions/workflows/moodle-ci.yml/badge.svg)](https://github.com/Snickser/moodle-paygw_robokassa/actions/workflows/moodle-ci.yml)

![alt text](https://raw.githubusercontent.com/Snickser/moodle-paygw_robokassa/da4ffeef22702ad4e087ca6ed78133f6c48dde65/pix/img.svg)

https://robokassa.com

## Возможности

+ Можно использовать пароль или кнопку для обхода платежа.
+ Сохраняет в базе номер курса и название группы студента.
+ Можно указать рекомендуемую цену, ограничить максимальную цену, или включить режим фиксированной цены.
+ Отображание продолжительности обучения (для enrol_fee и mod_gwpaymets), если она установлена.
+ Конвертация валют на стороне банка (usd, eur, kzt).
+ Поддержка пароля из модуля курса (mod_gwpaymets).
+ Оповещение пользователя при успешном платеже.
+ Рекуррентные платежи (только для платежей в рублях, используйте мой модуль report_payments для поиска и отмены таких платежей).

## Рекомендации

+ Moodle 4.3+
+ Для записи в курс используйте мой пропатченный плагин "Зачисление за оплату" [enrol_yafee](https://github.com/Snickser/moodle-enrol_yafee).
+ Для контрольного задания используйте пропатченный мной плагин по ссылке [mod_gwpayments](https://github.com/Snickser/moodle-mod_gwpayments/tree/dev).
+ Для ограничения доступности используйте пропатченный мной плагин по ссылке [availability_gwpayments](https://github.com/Snickser/moodle-availability_gwpayments/tree/dev).
+ Плагин просмотра отчётов и отключения регулярных платежей [report_payments](https://github.com/Snickser/moodle-report_payments/tree/dev).

## INSTALLATION

Download the latest **paygw_robokassa.zip** and unzip the contents into the **/payment/gateway** directory. Or upload it from Moodle plugins adminnistration interface.

1. Install the plugin
2. Enable the Robokassa payment gateway
3. Create a new payment account
4. Configure the payment account against the Robokassa gateway using your pay ID
5. Enable the 'Enrolment on Payment' enrolment method
6. Add the 'Enrolment on Payment' method to your chosen course
7. Set the payment account, enrolment fee, and currency

This plugin supports only basic functionality, but everything changes someday...
