<?php

class Pattern {
  private $data;

  public function __construct($file) {
    $this->data = $this->process($file);
  }

  private function process(string $file) : array {
    $data   = file_get_contents($file);
    $ranges = explode(',', $data);

    $data = [];
    foreach ($ranges as $range) {
      list($start, $end) = explode('-', $range);      
      $data[] = [$start, $end];
    }

    return $data;
  }

  public function getData() {
    return $this->data;
  }

  public function find($input) : bool {
    $length = strlen(intval($input));
    $first  = 1;
    $second = 2;

    if ($length % 2 == 0) {
      $mid_point = $length/2;
      $first     = substr($input, 0, $mid_point);
      $second    = substr($input, $mid_point);  
    }  

    return ($first == $second) ? true : false;  
  }

  public function find2($input) : bool {
    $pattern = '~\A(?= (\d+) \1+ (\d*) \z ) (?<pattern> \d+? ) \3+ (?! .+ \2 \z) (?= (?<trailing> \d* ) ) ~x';

    preg_match($pattern, $input, $matches);

    return (isset($matches['pattern']) && $matches['trailing'] == '') ? true : false;  
  }
}

$sum  = 0;
$sum2 = 0;

$pattern = new Pattern('./input.txt');

foreach ($pattern->getData() as list($start, $end)) {
  for ($i = $start; $i <= $end; $i++) {
    if ($pattern->find((string) $i)) {
      $sum += $i;
    }

    if ($pattern->find2((string) $i)) {      
      $sum2 += $i;
    }
  }
}

print '<p>Q1: The sum of invalid code is ' . $sum . '</p>';
print '<p>Q2: The sum of invalid code is ' . $sum2 . '</p>';
