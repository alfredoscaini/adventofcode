<?php

class Computer {
  private $registerA;
  private $registerB = 0;
  private $registerC = 0;
  private $program   = [];

  private $instructions = [
    'adv',
    'bxl',
    'bst',
    'jnz',
    'bxc',
    'out',
    'bdv',
    'cdv'
  ];

  public function __construct($datafile = '') {
    list($this->registerA, $this->program) = $this->processInput($datafile);
  }

  public function getProgram() : array {
    return $this->program;
  }

  public function run($copy = null) : array {
    $output  = [];
    $program = $this->program;
    $combo   = [0, 1, 2, 3, &$this->registerA, &$this->registerB, &$this->registerC, null];

    $index = 0;
    $program_count = count($this->program);

    if ($copy) {
      $this->registerA = $copy;
    }
    
    do {
      $operand   = $program[$index++];
      $argument  = $program[$index++];
      $operation = $this->instructions[$operand];

      switch ($operation) {
        case 'adv':
          $this->registerA = $this->adv($this->registerA, $combo[$argument]);
          break;
        case 'bxl':
          $this->registerB = $this->bxl($this->registerB, $argument);
          break;
        case 'bst':
          $this->registerB = $this->bst($combo[$argument]);
          break;
        case 'jnz':
          $index = $this->jnz($this->registerA, $argument, $index);
          break;
        case 'bxc':
          $this->registerB = $this->bxc($this->registerB, $this->registerC);
          break;
        case 'out':
          $output[] = $this->out($combo[$argument]);
          break;
        case 'bdv':
          $this->registerB = $this->bdv($this->registerA, $combo[$argument]);
          break;
        case 'cdv':
          $this->registerC = $this->cdv($this->registerA, $combo[$argument]);
          break;
      }
    } while ($index < $program_count);

    return $output;
  }

  private function processInput($datafile) : array {
    $data = explode("\n\n", file_get_contents($datafile));

    $registerA = explode("\n", $data[0])[0];
    $registerA = str_replace('Register A: ', '', $registerA);

    $program   = array_map( fn($a) => intval($a), explode(',', str_replace('Program: ', '', $data[1])));

    return [$registerA, $program];
  }

  private function adv($numerator, $denominator) : int {
    return floor($numerator / 2 ** $denominator);
  }

  private function bxl($numerator, $denominator) : int {
    return $numerator ^ $denominator;
  }

  private function bst($value) : int {
    return $value % 8;
  }

  private function jnz($check, $new, $old) : int {
    return ($check !== 0) ? $new : $old;
  }

  private function bxc($num1, $num2) : int {
    return $num1 ^ $num2;
  }

  private function out($value) : int {
    return $value % 8;    
  }

  private function bdv($numerator, $denominator) : int {
    return floor($numerator / 2 ** $denominator);
  }

  private function cdv($numerator, $denominator) : int {
    return floor($numerator / 2 ** $denominator);
  }
}

// ------------------------------------------------------------------------
// Main
$computer = new Computer('input.txt');

$output = $computer->run();
print '<p>Q1: The answer is ' . implode(',', $output) . '</p>';

$initial_value = 0;
$program = $computer->getProgram();

for ($i = 1; $i <= count($program); $i++) {
  for ($copy = $initial_value * 8; $copy <= ($initial_value + 1) * 9; $copy++) {
    $output = $computer->run($copy);

    if (array_slice($program, -$i) == $output) {
      $initial_value = $copy;
      break;
    }
  }
}

print '<p>Q2: The answer is ' . $initial_value . '</p>';