# MonoBank payment gateway plugin for Moodle.

## Features

+ Payment via MonoBank acquiring API (Visa, Mastercard, Apple Pay, Google Pay).
+ Supports UAH, USD, EUR currencies.
+ Can use a password or button to bypass payment.
+ Saves course number and student group name in the database.
+ Can set a recommended price, limit maximum price, or enable fixed price mode.
+ Displays training duration (for enrol_yafee and mod_gwpayments) if set.
+ User notification on successful payment.
+ Support for course module password (mod_gwpayments).

## Requirements

+ Moodle 4.0.9+
+ MonoBank merchant account with API token

## Recommended plugins

+ For course enrolment use [enrol_yafee](https://moodle.org/plugins/enrol_yafee)
+ For assignment payments use [mod_gwpayments](https://github.com/Snickser/moodle-mod_gwpayments/tree/dev)
+ For availability restrictions use [availability_gwpayments](https://github.com/Snickser/moodle-availability_gwpayments/tree/dev)

## Installation

Download the latest **paygw_monobank.zip** and unzip the contents into the **/payment/gateway** directory. Or upload it from Moodle plugins administration interface.

1. Install the plugin
2. Enable the MonoBank payment gateway
3. Create a new payment account
4. Configure the payment account against the MonoBank gateway using your API token
5. Enable the 'Enrolment on Payment' enrolment method
6. Add the 'Enrolment on Payment' method to your chosen course
7. Set the payment account, enrolment fee, and currency
