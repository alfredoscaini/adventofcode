<?php

class Manual {
  private $ordering;
  private $page_numbers;
  public $updates = [
    'good' => [],
    'bad'  => []
  ];

  public function __construct($ordering = [], $page_numbers = []) {
    $this->ordering     = $ordering;
    $this->page_numbers = $page_numbers;
    $this->setUpdates();
  }

  public function getMiddleNumberCount($set = 'good') : int {
    $sum = 0;

    foreach ($this->updates[$set] as $update) {      
      if (count($update)) {
        $sum += $update[ceil(count($update) / 2) - 1];
      }
    }

    return $sum;
  }

  private function setUpdates() {

    for ($i = 0; $i < count($this->page_numbers); $i++) {
      $numbers = $this->page_numbers[$i];

      $valid   = true;
      for ($j = 0; $j < count($numbers); $j++) {
        $number      = $numbers[$j];
        $next_number = (isset($numbers[$j+1])) ? $numbers[$j+1] : false;

        if ($this->check($number, $next_number) > 0) {
          $valid = false;
          break;
        }
      }

      usort($numbers, fn($a, $b) => $this->check($a, $b, $this->ordering));

      if ($valid) {
        $this->updates['good'][] = $numbers;
      } else {
        $this->updates['bad'][] = $numbers;
      }
    }
  }
    
  private function check($number, $next_number) : int {
    foreach ($this->ordering as $set) {
      $first  = $set[0];
      $second = $set[1];

      if (in_array($number, $set) && in_array($next_number, $set)) {
        $index_first = array_search($number, $set);
        $index_next  = array_search($next_number, $set);

        return $index_first - $index_next;
      }
    }

    return 0;
  }

}


// ----------------------------------------------------------------
// MAIN
$page_numbers = [];
$ordering     = [];

$handle = fopen("input.txt", "r");
if ($handle) {
  while (($line = fgets($handle)) !== false) {
    if (strstr($line, '|')) {
      $ordering[] = explode('|', $line);      
    } elseif (strstr($line, ',')) {
      $page_numbers[] = explode(',', $line);
    }
  }  
  fclose($handle);
}

$manual = new Manual($ordering, $page_numbers);

print '<p>Q1: The first answer is ' . $manual->getMiddleNumberCount() . '</p>';
print '<p>Q1: The second answer is ' . $manual->getMiddleNumberCount('bad') . '</p>';