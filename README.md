# BudgetPHP

Hacked together on a Sunday afternoon to visualize our creditcard statements and spending 😬😬😬😬

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

## Uses
- ChartJs
- Bootstrap
- Raw PHP

## Screenshot

<img width="722" alt="image" src="https://user-images.githubusercontent.com/14119296/119254636-852e5380-bc0b-11eb-98b5-ef102c6dd2ed.png">
