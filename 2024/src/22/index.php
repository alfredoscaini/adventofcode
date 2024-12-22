<?php

class Buyer {
  const MODULO = 16777216;

  private $secrets;

  public function __construct() {
    $this->secrets = [];
  }

  public function findSecret($number) : int {
    $secret = $this->update($number);
    $this->secrets[] = $secret;

    return $secret;
  }

  public function getLastSecret(): int {
    $last_index = count($this->secrets) - 1;

    return $this->secrets[$last_index];
  }

  public function getSequences($index = 0) : array {
    $sequences = [];
    $change    = [];

    for ($i = 1; $i < count($this->secrets); $i++) {
      $current = (int) substr($this->secrets[$i], -1);
      $last    = (int) substr($this->secrets[$i - 1], -1);

      $change[] = $current - $last;

      if (count($change) == 4) {
        $sequences[implode('.', $change)][$index] ??= $current;
        array_shift($change);
      }
    }

    return $sequences;
  }

  private function update($number) : int {
    $number = ($number ^ ($number << 6)) % self::MODULO;
    $number = ($number ^ ($number >> 5)) % self::MODULO;
    $number = ($number ^ ($number << 11)) % self::MODULO;

    return $number;
  }
}

// ----------------------------------
// Main

$data      = explode("\n", file_get_contents('input.txt'));
$sum       = 0;
$sequences = [];

foreach ($data as $index => $secret) {
  $buyer  = new Buyer();

  for ($i = 0; $i < 2000; $i++) {
    $secret = $buyer->findSecret($secret);    
  }

  $sum += $buyer->getLastSecret();
  $sequences = array_merge_recursive($sequences, $buyer->getSequences($index));
}

print '<p>Q1: The answer is ' . $sum . '</p>';

$sum = max(array_map('array_sum', $sequences));
print '<p>Q2: The answer is ' . $sum . '</p>';