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
 * Strings for component 'paygw_monobank', language 'ru'
 *
 * @package    paygw_monobank
 * @copyright  2024 Alex Orlov <snickser@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['abouttopay'] = 'Вы собираетесь оплатить';
$string['amount'] = 'Сумма:';
$string['api_token'] = 'API Токен (X-Token)';
$string['api_token_help'] = 'API токен из вашего мерчант-аккаунта МоноБанк. Получить можно на https://web.monobank.ua/ или тестовый токен на https://api.monobank.ua/';
$string['callback_help'] = 'Этот URL автоматически передаётся в МоноБанк при создании счёта. Ручная настройка не требуется.';
$string['callback_url'] = 'WebHook URL:';
$string['donate'] = '<div>Версия плагина: {$a->release} ({$a->versiondisk})</div>';
$string['enrolperiod'] = 'Продолжительность обучения ({$a->desc}): {$a->count}';
$string['error_txdatabase'] = 'Ошибка записи данных транзакции в базу';
$string['fixcost'] = 'Режим фиксированной цены';
$string['fixcost_help'] = 'Отключает для студентов возможность оплаты произвольной суммой.';
$string['fixdesc'] = 'Фиксированный комментарий платежа';
$string['fixdesc_help'] = 'Эта настройка устанавливает фиксированный комментарий для всех платежей.';
$string['gatewaydescription'] = 'МоноБанк — украинский платёжный шлюз для обработки карточных транзакций.';
$string['gatewayname'] = 'МоноБанк';
$string['istestmode'] = 'Тестовый режим';
$string['istestmode_help'] = 'В тестовом режиме для оплаты можно использовать любой номер карты, валидный по алгоритму Луна.';
$string['maxcost'] = 'Максимальная цена';
$string['maxcosterror'] = 'Максимальная цена должна быть выше рекомендуемой цены';
$string['message_invoice_created'] = 'Здравствуйте, {$a->firstname}!
Ссылка для оплаты {$a->orderid} на {$a->localizedcost} успешно создана.';
$string['message_success_completed'] = 'Здравствуйте, {$a->firstname}!
Ваш платёж (id {$a->orderid}) на {$a->localizedcost} успешно завершён.
Если элемент курса недоступен, обратитесь в техподдержку сайта.';
$string['messagesubject'] = 'Уведомление о платеже ({$a})';
$string['password'] = 'Резервный пароль';
$string['password_error'] = 'Введён неверный платёжный пароль';
$string['password_help'] = 'С помощью этого пароля можно обойти процесс оплаты. Может быть полезен когда нет возможности произвести оплату.';
$string['password_success'] = 'Платёжный пароль принят';
$string['password_text'] = 'Если у вас нет возможности совершить оплату, то попросите у вашего куратора пароль и введите его.';
$string['passwordmode'] = 'Разрешить ввод резервного пароля';
$string['payment'] = 'Оплата';
$string['payment_error'] = 'Ошибка создания платежа, попробуйте ещё раз или обратитесь в тех.поддержку.';
$string['payment_success'] = 'Оплата успешно произведена';
$string['paymore'] = 'Если вы хотите заплатить больше, то просто впишите свою сумму вместо указанной.';
$string['pluginname'] = 'Платежи МоноБанк';
$string['pluginname_desc'] = 'Плагин позволяет получать платежи через эквайринг МоноБанк.';
$string['sendlinkmsg'] = 'Отправлять ссылку оплаты на почту';
$string['sendlinkmsg_help'] = 'Если включено, то ссылка на счёт для оплаты будет отправляться на почту пользователя.';
$string['sendpaymentbutton'] = 'Оплатить через МоноБанк!';
$string['showduration'] = 'Показывать длительность обучения на странице';
$string['skipmode'] = 'Показать кнопку обхода платежа';
$string['skipmode_help'] = 'Эта настройка разрешает кнопку обхода платежа, может быть полезна в публичных курсах с необязательной оплатой.';
$string['skipmode_text'] = 'Если вы не имеете возможности совершить оплату через платёжную систему, то можете нажать на кнопку ниже.';
$string['skippaymentbutton'] = 'Не имею возможности :(';
$string['suggest'] = 'Рекомендуемая цена';
$string['usedetails'] = 'Показывать свёрнутым';
$string['usedetails_help'] = 'Прячет кнопку или пароль под сворачиваемый блок, если они включены.';
$string['usedetails_text'] = 'Нажмите тут если у вас нет возможности совершить оплату';
