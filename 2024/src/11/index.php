<?php 

trait Helper {
  public function processInput($datafile = '') {
    if (file_exists($datafile)) {
      $contents = file_get_contents($datafile);
      $data = explode(' ', $contents);
    }

    return $data;
  }

  public function showWork($data, $hr = false, $exit = false) : void {
    print '<pre>';
    print_r($data);
    print '</pre>';

    if ($hr) {
      print '<hr />';
    }

    if ($exit) {
      exit;
    }
  }
}

class Blink {
  use Helper;

  const MULTIPLIER = 2024;
  const FIRST_RULE_KEY = 1;

  private $stones = [];
  private $data   = [];

  public function __construct($datafile = '') {
    $data = $this->processInput($datafile);
    $this->stones = $this->arrangeIntoKeys($data);
  }

  private function arrangeIntoKeys($data = []) : array {
    $keys = [];
    foreach ($data as $stone) {
      $keys = $this->update($stone, $keys, 1);
    }

    return $keys;
  }

  public function getStones() : array {
    return $this->stones;
  }  

  public function blink(&$stones = [], $count = 0) : array {    

    $data = [];
    foreach ($stones as $id => $increment) {
      if ($id == 0) {
        $data = $this->update(self::FIRST_RULE_KEY, $data, $increment);
      } elseif (strlen($id) %2 == 0) {
        $split = str_split($id);
        $index = count($split) / 2;

        $left_stone  = intval(join('', array_slice($split, 0, $index)));
        $right_stone = intval(join('', array_slice($split, $index)));

        $data = $this->update($left_stone, $data, $increment);
        $data = $this->update($right_stone, $data, $increment);
      } else {
        $new_key = $id * self::MULTIPLIER;
        $data = $this->update($new_key, $data, $increment);
      }
    }

    if ($count > 1) {
      $data = $this->blink($data, --$count);
    }

    return $data;
  }

  public function update($id, $data, $increment) : array {
    if (!array_key_exists($id, $data)) {
      $data[$id] = 0;
    }

    $data[$id] += $increment;

    return $data;
  }
}


// -------------------------------------------------------
// Main
$blink = new Blink('input.txt');
$stones = $blink->getStones();

$data = $blink->blink($stones, 25);
print '<p>Q1: The answer is ' . array_sum($data) . '</p>';

$data = $blink->blink($stones, 75);
print '<p>Q2: The answer is ' . array_sum($data) . '</p>';