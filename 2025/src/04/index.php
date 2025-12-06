<?php

class Map {
  private $data;
  public const PAPER = '@';
  public const SPACE = '.';

  public function __construct($file) {
    $this->data = $this->process($file);
  }

  private function process(string $file) : array {
    $data = file_get_contents($file);
    $data = explode("\n", $data);

    for($i = 0; $i < count($data); $i++) {
      $data[$i] = str_split($data[$i]);
    }

    return $data;
  }

  public function getData() {
    return $this->data;
  }

  private function setAdjacent($x = 0, $y = 0) : array {
    $adjacent = [
      'TOP'          => [0, 0],
      'TOP_LEFT'     => [0, 0],
      'TOP_RIGHT'    => [0, 0],      
      'BOTTOM'       => [0, 0],
      'BOTTOM_LEFT'  => [0, 0],
      'BOTTOM_RIGHT' => [0, 0],
      'LEFT'         => [0, 0],
      'RIGHT'        => [0, 0]
    ];

    $adjacent['TOP']          = [$x - 1, $y];
    $adjacent['BOTTOM']       = [$x + 1, $y];

    $adjacent['TOP_LEFT']     = [$x - 1, $y - 1];
    $adjacent['TOP_RIGHT']    = [$x - 1, $y + 1];
    
    $adjacent['BOTTOM_LEFT']  = [$x + 1, $y - 1];
    $adjacent['BOTTOM_RIGHT'] = [$x + 1, $y + 1];

    $adjacent['LEFT']         = [$x, $y - 1];
    $adjacent['RIGHT']        = [$x, $y + 1];

    foreach ($adjacent as $key => $coords) {
      $position_x = $coords[0];
      $position_y = $coords[1];
      if (!isset($this->data[$position_x][$position_y])) {
        $adjacent[$key] = NULL;
      }
    }
    
    return $adjacent;
  }

  public function findPaper($map, $reduce = false) : int {
    $result  = 0;    

    for ($x = 0; $x < count($map); $x++) {
      $row = $map[$x];

      for ($y = 0; $y < count($row); $y++) {
        if ($map[$x][$y] !== self::PAPER) {
          continue;
        }

        $adjacent = $this->setAdjacent($x, $y);
        $limit    = 8;
        foreach ($adjacent as $key => $coords) {
          if (is_null($adjacent[$key])) {
            $limit--;            
          } else {          
            $position_x = $coords[0];
            $position_y = $coords[1];

            if ($map[$position_x][$position_y] == self::SPACE) {
              $limit--;            
            }
          }
        }

        if ($limit < 4) {
          if ($reduce) { $map[$x][$y] = self::SPACE; }
          $result++;
        }        
      }
    }

    if ($reduce && $result !== 0) {
      $result += $this->findPaper($map, true);
    }

    return $result;
  }
}

$sum  = 0;
$sum2 = 0;

$map  = new Map('./input.txt');
$sum  = $map->findPaper($map->getData(), false);
$sum2 = $map->findPaper($map->getData(), true);


print '<p>Q1: The max paper is ' . $sum . '</p>';
print '<p>Q2: The max paper is ' . $sum2 . '</p>';