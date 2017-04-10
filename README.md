# Name-String-Order
Attempts to order a person's name as 'First Last', 'Last First', and extracts 'First' and 'Last'.

## Usage

```php
use nameStringOrder\nameStringOrder;

$nameObj = new nameStringOrder('WANG jing-jing');

echo $nameObj->getFirstLast(); # Jing-Jing Wang

echo $nameObj->getLastFirst(); # Wang Jing-Jing

echo $nameObj->getFirst();     # Jing-Jing

echo $nameObj->getLast();      # Wang
```
