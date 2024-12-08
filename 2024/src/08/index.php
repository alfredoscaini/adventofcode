<?php

class Map {

  private $map;
  private $frequencies;
  private $antinodes;
  private $boundaries        = [];
  private $frequency_pattern = '/[0-9a-z]+/i';

  public function __construct($data = []) {
    $this->map = $data;
    
    $this->boundaries  = [
      'y' => count($this->map), 
      'x' => count($this->map[0]) - 1
    ];

    $this->frequencies = $this->findFrequencies($this->map);        
  }

  public function findNodes($allow_harmonics = false) : int {
    $allow_harmonics = ($allow_harmonics) ? true : false;
    $this->antinodes = $this->findAntinodes($this->map, $this->frequencies, $allow_harmonics);

    return array_sum(array_map( fn($a) => count($a), $this->antinodes));
  }

  private function findFrequencies($map = []) : array {
    $frequencies = [];

    for ($i = 0; $i < count($map); $i++) {
      for ($j = 0; $j < count($map[$i]); $j++) {
        if (preg_match($this->frequency_pattern, $map[$i][$j])) {
          $frequencies[$map[$i][$j]][] = [$i, $j];
        }
      }
    }

    return $frequencies;
  }

  private function findAntinodes($map = [], $frequencies = [], $allow_harmonics = false) : array {
    $antinodes = [];

    foreach ($frequencies as $frequency => $coordinates) {

      for ($current = 0; $current < count($coordinates); $current++) {
        for ($next = 0; $next < count($coordinates); $next++) {
          
          $positions = $this->findPositions($coordinates[$current], $coordinates[$next], $allow_harmonics);

          foreach ($positions as $coords) {
            $antinodes[$coords[0]][$coords[1]] = $frequency;
          }
        }
      }
    }

    return $antinodes;
  }

  private function findPositions($current, $next, $allow_harmonics) : array {
    $positions  = [];

    $displacement_y = $current[0] - $next[0];
    $displacement_x = $current[1] - $next[1];

     // If finding harmoncis, expand the search to include the entire map; else only check within the existing displacement.
    $expansion = ($allow_harmonics) ? $this->boundaries['y'] : 1;

    for ($expand = 1; $expand <= $expansion; $expand++) {      
      $expanded_displacement_y = $expand * $displacement_y;
      $expanded_displacement_x = $expand * $displacement_x;

      $displacement = [$expanded_displacement_y, $expanded_displacement_x];

      $antinodes = [                
        'current-bottom'  => array_map(fn($a, $b) => ( $a + $b ), $current, $displacement),
        'current-top'     => array_map(fn($a, $b) => ( $a - $b ), $current, $displacement),
        'next-bottom'     => array_map(fn($a, $b) => ( $a + $b ), $next, $displacement),
        'next-top'        => array_map(fn($a, $b) => ( $a - $b ), $next, $displacement)        
      ];

      if ($coordinates = $this->findResonances($current, $next, $antinodes, $allow_harmonics)) {
        $positions[] = $coordinates;
      }
    }

    return $positions;
  }

  private function findResonances($current, $next, $antinodes, $allow_harmonics) : array|false {
    $y_boundary = $this->boundaries['y'];
    $x_boundary = $this->boundaries['x'];

    $current_y = $current[0];
    $current_x = $current[1];
    $next_y    = $next[0];
    $next_x    = $next[1];

    foreach ($antinodes as $id => $coordinates) {
      $y = $coordinates[0];
      $x = $coordinates[1];
      
      $within_boundary   = ($y >= 0 && $y < $y_boundary && $x >= 0 && $x < $x_boundary) ? true : false;
      $too_close         = (in_array($y, [$current_y, $current_y]) || in_array($x, [$current_x, $next_x])) ? true : false;
            
      if ($within_boundary && (!$too_close || $allow_harmonics)) {
        return [$y, $x];
      }
    }

    return false;
  }
}


// ----------------------------------------------------
// MAIN
$data   = [];
$handle = fopen("input.txt", "r");

if ($handle) {
  while (($line = fgets($handle)) !== false) {
    $data[] = str_split($line);
  }  
  fclose($handle);
}

$map = new Map($data);

$answer           = $map->findNodes();
$answer_harmonics = $map->findNodes(true);

print '<p>Q1: The first answer is ' . $answer . '</p>';
print '<p>Q1: The second answer is ' . $answer_harmonics . '</p>';