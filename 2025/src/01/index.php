<?php 

class Dial {
  private $data;

  public const LEFT      = "L";
  public const RIGHT     = "R";
  public const START     = 50;
  public const FULL_TURN = 100;

  public function __construct($file) {
    $this->data = $this->process($file);
  }

  private function process(string $file) : array {
    $data   = file_get_contents($file);
    return explode("\n", $data);
  }

  private function rotations() {
    foreach ($this->data as $turn) {
      $direction   = (substr($turn, 0, 1) == self::LEFT) ? -1 : 1;
      $rotations[] = $direction * intval(substr($turn, 1));
    }

    return $rotations;
  }

  public function rotate() : int {
    $rotations = $this->rotations();
    $target    = 0;
    $position  = self::START;

    foreach ($rotations as $turn) {
      $position = ($position + $turn) % self::FULL_TURN;
      if ($position == 0) {
        $target++;
      }      
    }

    return $target;
  }

  public function rotate2() : int {
    $rotations = $this->rotations();
    $target    = 0;
    $position  = self::START;

    foreach ($rotations as $turn) {
      $max = max($position, $position + $turn);
      $min = min($position, $position + $turn);

      for ($i = $min; $i <= $max; $i++) {
        if ($i % self::FULL_TURN == 0) {
          $target++;
        }
      }

      $position = ($position + $turn) % self::FULL_TURN;
      if ($position < 0) {
        $position = self::FULL_TURN + $position;
      } elseif ($position == 0) {
        // the loop will count this again, so we have to decrement in order not to duplicate the counts
        $target--;
      }
    }

    return $target;
  }
}

// -------------------------------------------------------
// Main
// -------------------------------------------------------

$dial = new Dial('./input.txt');

print '<p>Q1: The answer is ' . $dial->rotate() . '</p>';
print '<p>Q2: The answer is ' . $dial->rotate2() . '</p>';