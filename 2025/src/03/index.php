<?php

class Joltage {
  private $data;

  public function __construct($file) {
    $this->data = $this->process($file);
  }

  private function process(string $file) : array {
    $data = file_get_contents($file);
    $data = explode("\n", $data);

    for($i = 0; $i < count($data); $i++) {
      $data[$i] = str_split($data[$i]);
    }

    return $data;
  }

  public function getData() {
    return $this->data;
  }

  public function findMaxJoltage($bank, $count) : int {
    $result = '';

    if (count($bank) == $count) {
      $result = implode($bank);
    } elseif ($count == 1) {
      $result = max($bank);
    } else {    
      $joltages  = array_slice($bank, 0, -($count-1));
      $max       = max($joltages);
      $new_count = strpos(implode($joltages), $max);      
      $result   .= $max;     
    }

    if (strlen($result) < $count) {
      $bank    = array_slice($bank, $new_count+1);
      $result .= $this->findMaxJoltage($bank, $count-1);
    }

    return (int) $result;
  }
}

$sum  = 0;
$sum2 = 0;

$joltage = new Joltage('./input.txt');

foreach ($joltage->getData() as $bank) {
  $sum += $joltage->findMaxJoltage($bank, 2);
}

foreach ($joltage->getData() as $bank) {
  $sum2 += $joltage->findMaxJoltage($bank, 12);
}

print '<p>Q1: The max joltage is ' . $sum . '</p>';
print '<p>Q2: The max joltage is ' . $sum2 . '</p>';