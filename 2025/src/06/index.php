<?php

class Worksheet {
  private $numbers   = [];
  private $operators = [];

  public const MULTIPLY  = "*";
  public const ADD       = "+";
  
  public function __construct($file, $column = false) {
    list($this->numbers, $this->operators) = ($column) ? $this->processColumns($file) : $this->process($file);
  }

  private function process(string $file) : array {
    $data = file_get_contents($file);
    $data = explode("\n", $data);

    $numbers   = [];
    $operators = [];

    for ($i = 0; $i < count($data) ; $i++) {

      $pattern = ($i == count($data) -1) ? '/\+|\*/' : '/\d+/';

      preg_match_all($pattern, $data[$i], $row);

      for ($j = 0; $j < count($row[0]); $j++) {
        if ($i == count($data) - 1) {    
          $operators = $row[0];
        } else {
          $numbers[$j][$i] = $row[0][$j];
        }      
      }
    }

    return [$numbers, $operators];
  }

  private function processColumns($file) : array {
    $data = file_get_contents($file);
    $data = explode("\n", $data);

    $operators    = array_pop($data);
    $numbers      = [];
    $placeholders = [];

    $pattern = '/\+|\*/';
    preg_match_all($pattern, $operators, $operators);

    $data = array_map('str_split', $data);

    for ($j = 0; $j < count($data[0]); $j++) {
      $placeholders[$j] = '';
      for ($i = 0; $i < count($data); $i++) {
        $placeholders[$j] = trim($placeholders[$j] . $data[$i][$j]);
      }
    }

    $j = 0;
    for ($i = 0; $i < count($placeholders); $i++) {
      if ( (int) $placeholders[$i] == 0) {
        $j++;
        continue;
      }

      $numbers[$j][$i] = (int) $placeholders[$i];
    }

    return [$numbers, $operators[0]];
  }

  public function getNumbers() {
    return $this->numbers;
  }

  public function getOperators() {
    return $this->operators;
  }
}


$worksheet = new Worksheet('./input.txt', false);

$numbers   = $worksheet->getNumbers();
$operators = $worksheet->getOperators();

$sum = 0;

for ($i = 0; $i < count($operators); $i++) {  
  if ($operators[$i] == Worksheet::MULTIPLY) {
    $sum += array_product($numbers[$i]);
  }

  if ($operators[$i] == Worksheet::ADD) {
    $sum += array_sum($numbers[$i]);
  }
}

print '<p>The total is ' . $sum . '</p>';

$worksheet = new Worksheet('./input.txt', true);

$numbers   = $worksheet->getNumbers();
$operators = $worksheet->getOperators();

$sum = 0;

for ($i = 0; $i < count($operators); $i++) {  
  if ($operators[$i] == Worksheet::MULTIPLY) {
    $sum += array_product($numbers[$i]);
  }

  if ($operators[$i] == Worksheet::ADD) {
    $sum += array_sum($numbers[$i]);
  }
}

print '<p>The total is ' . $sum . '</p>';