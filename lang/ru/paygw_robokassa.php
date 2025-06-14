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
 * Strings for component 'paygw_robokassa', language 'ru'
 *
 * @package    paygw_robokassa
 * @copyright  2024 Alex Orlov <snickser@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['abouttopay'] = 'Вы собираетесь пожертвовать на';
$string['amount'] = 'Сумма:';
$string['callback_help'] = 'Скопируйте это и поместите в URL-адреса технических настроек магазина Robokassa, метод отсылки - POST.';
$string['callback_url'] = 'ResultURL:';
$string['checkinvoice'] = 'Предпроверка invoice';
$string['checkinvoice_help'] = 'Эта опция включает дополнительную проверку инвойса в Робокассе при создании и получении платежа. При создании проверяется что такой платёж ещё не существует, а при получении подтверждения от банка делается запрос на проверку факта оплаты выставленного счёта.';
$string['cost'] = 'Стоимость записи';
$string['crypto'] = 'Алгоритм расчета хеша';
$string['crypto_help'] = 'Метод используемый для рассчёта хеша контролных сумм параметров оплаты, который обеспечивает безопасность при прохождении платежа и целостность передаваемых данных. <b>Убедитесь, что именно этот алгоритм указан в настройках магазина, иначе ничего работать не будет!</b>';
$string['currency'] = 'Валюта';
$string['donate'] = '<div>Версия плагина: {$a->release} ({$a->versiondisk})<br>
Новые версии плагина вы можете найти на <a href=https://github.com/Snickser/moodle-paygw_robokassa>GitHub.com</a>
<img src="https://img.shields.io/github/v/release/Snickser/moodle-paygw_robokassa.svg"><br>
Пожалуйста, отправьте мне немножко <a href="https://yoomoney.ru/fundraise/143H2JO3LLE.240720">доната</a>😊</div>
TRX TRGMc3b63Lus6ehLasbbHxsb2rHky5LbPe<br>
BTC 1GFTTPCgRTC8yYL1gU7wBZRfhRNRBdLZsq<br>
ETH 0x1bce7aadef39d328d262569e6194febe597cb2c9<br>
<iframe src="https://yoomoney.ru/quickpay/fundraise/button?billNumber=143H2JO3LLE.240720"
width="330" height="50" frameborder="0" allowtransparency="true" scrolling="no"></iframe><br>';
$string['enrolperiod'] = 'Продолжительность обучения ({$a->desc}): {$a->count}';
$string['error_txdatabase'] = 'Error write TX data to database';
$string['fixcost'] = 'Режим фиксированной цены';
$string['fixcost_help'] = 'Отключает для студентов возможность оплаты произвольной суммой.';
$string['fixdesc'] = 'Фиксированный комментарий платежа';
$string['fixdesc_help'] = 'Эта настройка устанавливает фиксированный комментарий для всех платежей, и отключает отображение описания комментария на странице платежа.';
$string['gatewaydescription'] = 'Robokassa — авторизованный платежный шлюз для обработки транзакций по кредитным картам.';
$string['gatewayname'] = 'Robokassa';
$string['inccurrlabel'] = 'Метод платежа';
$string['istestmode'] = 'Тестовый режим';
$string['istestmode_help'] = 'В тестовом режиме нет рекуррентных платежей.';
$string['maxcost'] = 'Максимальная цена';
$string['maxcosterror'] = 'Максимальная цена должна быть выше рекомендуемой цены';
$string['merchant_login'] = 'Идентификатор магазина';
$string['message_invoice_created'] = 'Здравствуйте, {$a->firstname}!
Счёт на оплату по ссылке {$a->orderid} на {$a->localizedcost} успешно создан.
Вы можете совершить платёж по этой ссылке в течении 30-ти минут.
Если в процессе оплаты вы получили код ошибки 40 - это значит что счёт уже оплачен, а код ошибки 33 говорит о том, что время для оплаты истекло.';
$string['message_recurrent_completed'] = 'Здравствуйте, {$a->firstname}!
Регулярный платёж № {$a->orderid} на {$a->localizedcost} создан и передан в банк.
Отключить регулярные платежи можно в разделе Отчёты о платежах в личном профиле.';
$string['message_recurrent_created'] = 'Здравствуйте, {$a->firstname}!
Регулярный платёж № {$a->orderid} на {$a->localizedcost} создан и передан в банк.
Отключить регулярные платежи можно в разделе Отчёты (оплата) в личном профиле {$a->url}/user/profile.php';
$string['message_recurrent_error'] = 'Здравствуйте, {$a->firstname}!
Регулярный платёж № {$a->orderid} на {$a->localizedcost} завершился с ошибкой.
Подписка была отключена, для возобновления подписки произведите новую оплату.';
$string['message_recurrent_notify'] = 'Здравствуйте, {$a->firstname}!
Напоминаем о том, что приближается дата регулярного платежа № {$a->orderid} на {$a->localizedcost}.
Пожалуйста, обеспечьте наличие указанной суммы на счёте, иначе подписка не будет продлена.
Отключить регулярные платежи можно в разделе Отчёты (оплата) в личном профиле {$a->url}/user/profile.php';
$string['message_success_completed'] = 'Здравствуйте, {$a->firstname}!
Ваша платёжная транзакция № {$a->orderid} на {$a->fee} {$a->currency} успешно завершена. Спасибо за ваше пожертвование.
Если элемент курса недоступен, обратитесь в техподдержку сайта.';
$string['message_success_recurrent'] = 'Здравствуйте, {$a->firstname}!
Регулярная платёжная транзакция № {$a->orderid} на {$a->localizedcost} успешно создана. Спасибо за ваше пожертвование.
Следующий автоматический платёж назначен на {$a->nextpay}.
Отключить регулярные платежи можно в разделе Отчёты (оплата) в личном профиле {$a->url}/user/profile.php';
$string['messagesubject'] = 'Уведомление о платеже ({$a})';
$string['noreportplugin'] = '<font color="red">Не установлен <a href="https://github.com/Snickser/moodle-report_payments">report_payments</a> плагин, вы не сможете отменять регулярные платежи.</font>';
$string['password'] = 'Резервный пароль';
$string['password1'] = 'Пароль1';
$string['password2'] = 'Пароль2';
$string['password_error'] = 'Введён неверный платёжный пароль';
$string['password_help'] = 'С помощью этого пароля можно обойти процесс отплаты. Может быть полезен когда нет возможности произвести оплату.';
$string['password_mode'] = 'Разрешить ввод резервного пароля';
$string['password_success'] = 'Платёжный пароль принят';
$string['password_text'] = 'Если у вас нет возможности сделать пожертвование, то попросите у вашего куратора пароль и введите его.';
$string['payment'] = 'Пожертвование';
$string['payment_error'] = 'Ошибка создания платежа, попробуйте ещё раз или обратитесь в тех.поддержку.';
$string['payment_success'] = 'Оплата успешно произведена';
$string['paymentserver'] = 'URL сервера оплаты';
$string['paymentsystem'] = 'Платежная система';
$string['paymore'] = 'Если вы хотите пожертвовать больше, то просто впишите свою сумму вместо указанной.';
$string['plastic'] = 'VISA, MasterCard, МИР';
$string['pluginname'] = 'Платежи Robokassa';
$string['pluginname_desc'] = 'Плагин позволяет получать платежи через Robokassa.';
$string['recurrent'] = 'Включить регулярные платежи';
$string['recurrent_help'] = 'Регулярные (рекуррентные) платежи исполняются по таймеру без участия студента, данные первого платежа сохраняются на стороне банка и используются повторно, с некоторой периодичностью.
<br><b>Работает только для рублей (без конвертации)! Неправильная настройка может привести к опустошению банковских счетов клиентов!</b>';
$string['recurrentcost'] = 'Стоимость регулярного платежа';
$string['recurrentcost1'] = 'Уплаченная';
$string['recurrentcost2'] = 'Стоимость элемента';
$string['recurrentcost3'] = 'Рекомендуемая цена';
$string['recurrentcost_help'] = 'Указывает какую цену брать при проведении регулярного платежа:<br>
Уплаченная - та, что была указана пользователем при создании регулярного платежа.<br>
Стоимость элемента - та, которая указана в настройках платёжного модуля или курса.<br>
Рекумендуемая - берётся из настроек этого интерфейса.';
$string['recurrentday'] = 'День ежемесячного платежа';
$string['recurrentday_help'] = 'Указывает день месяца в который будут происходить очередные списания. Если не установлено, то платежи будут производится по цикличному расписанию';
$string['recurrentdesc'] = 'Этот платёж будет регулярным! Cумма и дата следующего платежа может измениться.<br>
Отключить регулярные платежи вы можете в своём Профиле в разделе Отчёты "Оплата и регулярные подписки".';
$string['recurrentperiod'] = 'Периодичность регулярного платежа';
$string['recurrentperiod_help'] = 'Желательно указать периодичность не чаще чем раз в день. Это связано с выполеннием соответствующей регулярной задачи в планировщике задач. Чаще чем раз в день - только для тестов.';
$string['recurrentperioderror'] = 'Укажите периодичность';
$string['return_url'] = 'SuccessURL и FailURL:';
$string['robokassa'] = 'Robokassa';
$string['sbp'] = 'СБП';
$string['sendlinkmsg'] = 'Отправлять ссылку оплаты на почту';
$string['sendlinkmsg_help'] = 'Если включено, то ссылка на счёт для оплаты будет отправляться на почту пользователя.';
$string['sendpaymentbutton'] = 'Пожертвовать!';
$string['showduration'] = 'Показывать длительность обучения на странице';
$string['skipmode'] = 'Показать кнопку обхода платежа';
$string['skipmode_help'] = 'Эта настройка разрешает кнопку обхода платежа, может быть полезна в публичных курсах с необязательной оплатой.';
$string['skipmode_text'] = 'Если вы не имеете возможности совершить пожертвование через платёжную систему, то можете нажать на кнопку ниже.';
$string['skippaymentbutton'] = 'Не имею возможности :(';
$string['sno'] = 'Тип налогообложения';
$string['sno_help'] = 'Тип системы налогообложения для формирования чеков:<br>
1 - Общая система налогообложения<br>
2 - Упрощенная (УСН, доходы)<br>
3 - Упрощенная (УСН, доходы минус расходы)<br>
4 - Единый сельскохозяйственный налог (ЕСН)<br>
5 - Патентная система налогообложения';
$string['suggest'] = 'Рекомендуемая цена';
$string['suggesterror'] = 'Рекомендуемая цена должна быть указана для включенного регулярного платежа';
$string['tax'] = 'Ставка НДС';
$string['tax_help'] = 'Ставка НДС согласно API документации Робокасса.';
$string['testpassword1'] = 'Тестовый Пароль1';
$string['testpassword2'] = 'Тестовый Пароль2';
$string['uninterrupted_desc'] = 'Цена за курс сформирована с учётом пропущенного времени неоплаченного вами периода.';
$string['usedetails'] = 'Показывать свёрнутым';
$string['usedetails_help'] = 'Прячет кнопку или пароль под сворачиваемый блок, если они включены.';
$string['usedetails_text'] = 'Нажмите тут если у вас нет возможности совершить пожертвование';
