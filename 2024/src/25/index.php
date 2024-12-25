<?php

trait Pin {

  const ICON       = '#';
  const PIN_HEIGHT = 5;

  public function setPin($data = []) : array {
    $pin = [];

    foreach ($data as $row) {
      $row   = implode('', $row);
      $pin[] = substr_count($row, self::ICON) - 1;
    }

    return $pin;
  }

  public function transpose($data) : array {
    $transposed = [];
  
    foreach ($data as $row => $columns) {
      foreach ($columns as $row2 => $column2) {
        $transposed[$row2][$row] = $column2;
      }
    }

    return $transposed;
  }  
}

class Item {
  use Pin;

  private $key         = '';
  private $pin         = [];

  public function __construct($key = []) {
    $this->key = $key;
    $this->pin = $this->setPin($this->transpose($this->key));
  }

  public function getPin() : array {
    return $this->pin;
  }

  public function testPin($pin) : bool {
    $test0 = $pin[0] + $this->pin[0] <= self::PIN_HEIGHT;
    $test1 = $pin[1] + $this->pin[1] <= self::PIN_HEIGHT;
    $test2 = $pin[2] + $this->pin[2] <= self::PIN_HEIGHT;
    $test3 = $pin[3] + $this->pin[3] <= self::PIN_HEIGHT;
    $test4 = $pin[4] + $this->pin[4] <= self::PIN_HEIGHT;

    return ($test0 && $test1 && $test2 && $test3 && $test4) ? true : false;
  }
}

// --------------------------------------------------------------------
// Main
function findLocksAndKeys($data) : array {
  $lock_identity = '#####';
  $key_identity  = '.....';
  
  foreach ($data as $group) {
    $item = explode("\n", $group);
    $is_lock = ($item[0] == $lock_identity) ? true : false;
  
    $arr = [];
    foreach ($item as $index => $string) {
      $arr[$index] = str_split($string);
    }
  
    if ($is_lock) {
      $locks[] = new Item($arr);
    } else {
      $keys[]  = new Item($arr);
    }
  }

  return [$locks, $keys];
}


$data  = explode("\n\n", file_get_contents('input.txt'));

list($locks, $keys) = findLocksAndKeys($data);

$matches = [];
foreach ($locks as $lock) {
  foreach ($keys as $key) {
    if ($key->testPin($lock->getPin())) {
      $matches[] = $key;
    }
  }
}

print '<p>Q1: The answer is ' . count($matches) . '</p>';