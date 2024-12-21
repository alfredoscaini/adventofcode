<?php

class Design {
  private $patterns = [];
  private $designs   = [];
  private $match    = [];

  public function __construct($datafile = '') {
    list($this->patterns, $this->designs) = $this->processInput($datafile);    
  }

  public function find() : array {
    $sum = 0;
    $combinations = 0;

    foreach ($this->designs as $design) {
      $result = $this->findMatches($design);

      $combinations += $result;
      if ($result) {
        $sum++;
      }
    }

    return [$sum, $combinations];
  } 

  private function findMatches($design = '') : int {
    $sum          = 0;
    $found        = false;
    $patterns     = $this->patterns;
  
    if (isset($this->match[$design])) {
      $found = true;
      $sum   = $this->match[$design];
    }

    
    if (!$found) {
      foreach ($patterns as $pattern) {
        if (strpos($design, $pattern) === 0) {
          
          $new_design = substr($design, strlen($pattern));    

          if ($new_design === '') {
            $sum++;
            continue;
          }
    
          $new_design = $this->findMatches($new_design);
    
          if ($new_design === false) {
            continue;
          }
    
          $sum += $new_design;
        }
      }

      if (!isset($this->match[$design])) {
        $this->match[$design] = 0;
      }
      
      $this->match[$design] = $sum;
    }
  
    return $sum;
  }

  private function processInput($datafile = '') : array {
    $data = [];

    if (file_exists($datafile)) {
      $data     = explode("\n\n", file_get_contents('input.txt'));
	    $patterns = explode(', ', $data[0]);
      $designs  = explode("\n", $data[1]);

      $data = [$patterns, $designs];
    }

    return $data;
  }
}


// -----------------------------------------------------------
// Main

$design = new Design('input.txt');
list($sum, $combinations) = $design->find();

print '<p>Q1: The answer is ' . $sum . '</p>';
print '<p>Q2: The answer is ' . $combinations . '</p>';

