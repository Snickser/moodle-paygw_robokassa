<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Local language pack from https://study.bhuri.ru
 *
 * @package    paygw_robokassa
 * @subpackage robokassa
 * @copyright  2024 Alex Orlov <snickser@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['sendpaymentbutton'] = 'Пожертвовать!';
$string['skippaymentbutton'] = 'Не имею :(';
$string['paymore'] = 'Если вы хотите пожертвовать больше, то просто впишите свою сумму вместо указанной.';
$string['payment'] = 'Пожертвование';
$string['abouttopay'] = 'Вы собираетесь пожертвовать на';
$string['pluginname'] = 'Платежи Robokassa';
$string['pluginname_desc'] = 'Плагин Robokassa позволяет получать платежи через Robokassa.';
$string['gatewaydescription'] = 'Robokassa — авторизованный платежный шлюз для обработки транзакций по кредитным картам.';
$string['cost'] = 'Стоимость записи';
$string['currency'] = 'Валюта';
$string['paymentserver'] = 'URL сервера оплаты';
$string['password'] = 'Резервный пароль';
$string['passwordmode'] = 'Разрешить ввод резервного пароля';
$string['password_help'] = 'С помощью этого пароля можно обойти процесс отплаты. Может быть полезен когда нет возможности произвести оплату.';
$string['password_text'] = 'Если у вас нет возможности сделать пожертвование, то попросите у вашего куратора пароль и введите его.';
$string['password1'] = 'Пароль1';
$string['password2'] = 'Пароль2';
$string['merchant_login'] = 'Идентификатор магазина';
$string['istestmode'] = 'Тестовый режим';
$string['callback_url'] = 'ResultURL:';
$string['callback_help'] = 'Copy this and put it in URLs at your Robokassa account.';
$string['return_url'] = 'SuccessURL и FailURL:';
$string['payment_success'] = 'Оплата успешно произведена';
$string['payment_error'] = 'Ошибка оплаты';
$string['suggest'] = 'Рекомендуемая цена';
$string['password_success'] = 'Платёжный пароль принят';
$string['password_error'] = 'Введён неверный платёжный пароль';
$string['maxcost'] = 'Максимальная цена';
$string['skipmode'] = 'Показать кнопку обхода платежа';
$string['skipmode_help'] = 'Эта настройка разрешает кнопку обхода платежа, может быть полезна в публичных курсах с необязательной оплатой.';
$string['skipmode_text'] = 'Если вы не имеете возможности совершить пожертвование через платёжную систему то можете нажать на эту кнопку.';
$string['fixdesc'] = 'Фиксированный комментарий платежа';
$string['fixdesc_help'] = 'Эта настройка устанавливает фиксированный комментарий для всех платежей, и отключает отображение описания комментария на странице платежа.';
$string['showduration'] = 'Показывать длительность обучения на странице';
$string['usedetails'] = 'Показывать свёрнутым';
$string['usedetails_help'] = 'Прячет кнопку или пароль под сворачиваемый блок, если они включены.';
$string['usedetails_text'] = 'Нажмите тут если у вас нет возможности совершить пожертвование';
$string['inccurrlabel'] = 'Метод платежа';

/* Payment systems */
$string['paymentsystem'] = 'Платежная система';
$string['robokassa'] = 'Robokassa';
$string['plastic'] = 'VISA, MasterCard, МИР';
$string['sbp'] = 'СБП';

$string['tax'] = 'Ставка НДС';
$string['tax_help'] = 'Ставка НДС согласно API документации Робокасса.';
$string['sno'] = 'Тип налогообложения';
$string['sno_help'] = 'Тип системы налогообложения для формирования чеков:<br>
1 - Общая система налогообложения<br>
2 - Упрощенная (УСН, доходы)<br>
3 - Упрощенная (УСН, доходы минус расходы)<br>
4 - Единый сельскохозяйственный налог (ЕСН)<br>
5 - Патентная система налогообложения';
