<?php 

trait Helper {
  public function processInput($datafile = '') {
    if (file_exists($datafile)) {
      $contents = explode("\n", file_get_contents($datafile));

      $data    = [];
      $counter = 0;
      for ($i = 0; $i < count($contents); $i++) {
        if (empty($contents[$i])) {
          $counter++;
          $data[$counter] = [];
          continue;
        }

        $set = $this->process($contents[$i]);
        $data[$counter][] = $set;
      }
    }

    return $data;
  }

  public function process($data) : array {
    $coords = explode(',', $data);

    return [ 
      (int) preg_replace('/[^\-\d]*(\-?\d*).*/', '$1', $coords[0]), 
      (int) preg_replace('/[^\-\d]*(\-?\d*).*/', '$1', $coords[1])
    ];
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

class Arcade {
  use Helper;

  const BUTTON_A = 0;
  const BUTTON_B = 1;
  const PRIZE    = 2;
  const TOKEN_A  = 3;
  const TOKEN_B  = 1;

  private $games;


  public function __construct($datafile = '') {
    $this->games = $this->processInput($datafile);
  }

  public function getGames() : array {
    return $this->games;
  }

  public function updatePrizeCoordinates($count = 0) : void {
    for ($i = 0; $i < count($this->games); $i++) {
      $this->games[$i][self::PRIZE][0] += $count;
      $this->games[$i][self::PRIZE][1] += $count;
    }
  }

  public function findMinTokens($move = []) : int {  
    $tokens = 0;
    
    list($x_btn_a, $y_btn_a) = $move[0];
    list($x_btn_b, $y_btn_b) = $move[1];
    list($x_prize, $y_prize) = $move[2];

		$diff  = ($x_btn_a * $y_btn_b) - ($y_btn_a * $x_btn_b);
		$btn_a = (($x_prize * $y_btn_b) - ($y_prize * $x_btn_b)) / $diff;
		$btn_b = (($x_btn_a * $y_prize) - ($y_btn_a * $x_prize)) / $diff;

		if (is_int($btn_a) && is_int($btn_b)) {
			$tokens = ($btn_a * self::TOKEN_A) + ($btn_b * self::TOKEN_B);
		}    

    return $tokens;
  }

}


// -------------------------------------------------------
// Main
$arcade = new Arcade('input.txt');
$games  = $arcade->getGames();

$tokens = 0;
foreach ($games as $game) {
  $tokens += $arcade->findMinTokens($game);
}
print '<p>Q1: The answer is ' . $tokens . '</p>';

$arcade->updatePrizeCoordinates(10000000000000);
$games  = $arcade->getGames();

$tokens = 0;
foreach ($games as $game) {
  $tokens += $arcade->findMinTokens($game);
}
print '<p>Q2: The answer is ' . $tokens . '</p>';
