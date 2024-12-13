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

  private $stones = [];
  private $data   = [];

  public function __construct($datafile = '') {
    $this->stones = $this->processInput($datafile);
  }

  public function getStones() : array {
    return $this->stones;
  }

  public function blink(&$stones = [], $count = 0) : array {
    $data = [];

    foreach ($stones as $stone) {
      if ($stone == 0) {
        $stone = 1;
        $data[] = $stone;
      } elseif (strlen($stone) %2 == 0) {
        $split = str_split($stone);
        $index = count($split) / 2;

        $left_stone  = intval(join('', array_slice($split, 0, $index)));
        $right_stone = intval(join('', array_slice($split, $index)));

        $data[] = $left_stone;
        $data[] = $right_stone;
      } else {
        $data[] = $stone * self::MULTIPLIER;
      }
    }

    // print $this->showWork($data, true);

    if ($count > 1) {
      $data = $this->blink($data, --$count);
    }

    return $data;
  }

  public function count() : int {
    $count = 0;

    return $count;
  }
}


// -------------------------------------------------------
// Main

$blink = new Blink('input.txt');
$stones = $blink->getStones();

$stones = $blink->blink($stones, 25);
print '<p>Q1: The answer is ' . count($stones) . '</p>';

// doesn't work - have to redo my code
// $stones = $blink->blink($stones, 75);
// print '<p>Q2: The answer is ' . count($stones) . '</p>';