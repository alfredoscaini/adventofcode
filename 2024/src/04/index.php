<?php

class WordSearch {
  public $data;

  public function __construct($data = []) {
    $this->data = $data;
  }

  public function find($term = '') : int {
      $word  = str_split($term);
      $data  = $this->data;
      $max_y = count($data);
      $max_x = strlen($data[0]);
      $found = 0;
      
      for ($start_x = 0; $start_x < $max_x; $start_x++) {
        for ($start_y = 0; $start_y < $max_y; $start_y++) {        
          
          if (isset($data[$start_y][$start_x]) && $data[$start_y][$start_x] != $word[0]) { continue; }

          for ($diagonal_x = -1; $diagonal_x <= 1; $diagonal_x++) {
            for ($diagonal_y = -1; $diagonal_y <= 1; $diagonal_y++) {            

              if ($diagonal_x == 0 && $diagonal_y == 0) { continue; }

              $valid = true;
              $x     = $start_x;
              $y     = $start_y;
              
              for ($i = 1; $i < count($word); $i++) {
                $x += $diagonal_x;
                $y += $diagonal_y;

                $x_out_of_range = ($x < 0) || ($x >= $max_x) ? true : false;
                $y_out_of_range = ($y < 0) || ($y >= $max_y) ? true : false;
                
                if ($x_out_of_range || $y_out_of_range || ($data[$y][$x] != $word[$i]) ) {
                  $valid = false;
                  break;
                }
              }

              if ($valid) {
                $found++;
              }
            }
          }
        }
      }

      return $found;
  }

  public function find2($term = '') : int {
    $word  = str_split($term);
    $data  = $this->data;
    $max_y = count($data);
    $max_x = strlen($data[0]);
    $found = 0;

    for ($diagonal_x = 1; $diagonal_x < $max_x - 1; $diagonal_x++) {
      for ($diagonal_y = 1; $diagonal_y < $max_y - 1; $diagonal_y++) {
      
        if (!isset($data[$diagonal_y - 1][$diagonal_x - 1]) || !isset($data[$diagonal_y + 1][$diagonal_x + 1])) { continue; }

        if (isset($data[$diagonal_y][$diagonal_x]) && $data[$diagonal_y][$diagonal_x] == $word[1]) {
          $letter_01 = $data[$diagonal_y - 1][$diagonal_x - 1];
          $letter_02 = $data[$diagonal_y + 1][$diagonal_x + 1];
      
          $top_match_found         = ($letter_01 == $word[0] && $letter_02 == $word[2]) ? true : false;
          $top_reverse_match_found = ($letter_01 == $word[2] && $letter_02 == $word[0]) ? true : false;

          $letter_01 = $data[$diagonal_y - 1][$diagonal_x + 1];
          $letter_02 = $data[$diagonal_y + 1][$diagonal_x - 1];

          $bottom_match_found         = ($letter_01 == $word[0] && $letter_02 == $word[2]) ? true : false;
          $bottom_reverse_match_found = ($letter_01 == $word[2] && $letter_02 == $word[0]) ? true : false;
          
          if (($top_match_found || $top_reverse_match_found) && ($bottom_match_found || $bottom_reverse_match_found)) { 
            $found++;
          }
        }
      }
    }
    
    return $found;
  }
}

// ----------------------------------------------------------------
// MAIN
$data   = [];
$handle = fopen("input.txt", "r");
if ($handle) {
  while (($line = fgets($handle)) !== false) {
    $data[] = $line;
  }  
  fclose($handle);
}

$wordsearch = new WordSearch($data);

print '<p>Q1: The answer is ' . $wordsearch->find('XMAS') . '</p>';
print '<p>Q2: The answer is ' . $wordsearch->find2('MAS') . '</p>';