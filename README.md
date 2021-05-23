# BudgetPHP

Hacked together on a Sunday afternoon to visual our creditcard statements and spending 😬

Total WIP but initially works.

NOT optimized for efficiency or anything fancy yet. 

## Requirements
1. ANZ CSV Account Statements (Only supports CC atm)
2. PHP 7.4+

## Setup
1. In /Mapping, copy `categories.example.json` to `categories.json`
2. Add your mapping to the `categories.json` file
3. Add your statements to the /Statements Folder
4. Terminal `php -s 127.0.0.1:8111`
