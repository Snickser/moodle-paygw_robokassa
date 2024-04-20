[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://paypal.me/snickser) support me with a donation.


# Robokassa payment gateway plugin for Moodle.

Version 0.8

https://robokassa.com

![alt text](https://raw.githubusercontent.com/Snickser/moodle-paygw_robokassa/da4ffeef22702ad4e087ca6ed78133f6c48dde65/pix/img.svg)

Возможности
------------
+ Можно использовать пароль или кнопку для обхода платежа.
+ Сохраняет в базе номер курса и название группы студента.
+ Можно указать рекомендуемую цену.
+ Можно ограничить максимальную цену.
+ Отображание продолжительности обучения (для enrol_fee и mod_gwpaymets), если она установлена.
+ Конвертация валют на стороне банка (usd, eur, kzt).

В планах
---
+ передача номенклатуры

Рекомендации
------------
+ Тестировалось только с Moodle 4.3+
+ Для записи в курс подходит стандарный плагин "Зачисление за оплату" (enrol_fee).
+ Для контрольного задания используйте модуль "[Gateway Payments](https://moodle.org/plugins/mod_gwpayments)" (мои правки [mod_gwpayments](https://github.com/Snickser/moodle-mod_gwpayments/tree/dev)), он правда глючный, но других пока нет.
+ Для ограничения доступности используйте модуль "[PaymentS availability condition for paid access](https://moodle.org/plugins/availability_gwpayments)" (мои правки [availability_gwpayments](https://github.com/Snickser/moodle-availability_gwpayments/tree/dev)).


INSTALLATION
------------
Download the latest paygw_robokassa.zip and unzip the contents into the /payment/gateway directory. Or upload it from Moodle plugins adminnistration interface.

1. Install the plugin
2. Enable the Robokassa payment gateway
3. Create a new payment account
4. Configure the payment account against the Robokassa gateway using your pay ID
5. Enable the 'Enrolment on Payment' enrolment method
6. Add the 'Enrolment on Payment' method to your chosen course
7. Set the payment account, enrolment fee, and currency

This plugin supports only basic functionality, but everything changes someday...
