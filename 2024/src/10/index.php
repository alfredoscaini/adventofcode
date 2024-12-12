<?php

class Map {

  const SEQUENCE_START = 0;
  const SEQUENCE_END   = 9;
  
  private $trailheads;
  private $map;
  private $routes = [
    'left'   => [0, -1], 
    'right'  => [0,  1], 
    'bottom' => [1,  0], 
    'top'    => [-1, 0]
  ];
  
  public function __construct($datafile = '') {
    $this->map = $this->processMap($datafile);    
  }

  public function scores($ratings = false) : int {
    $ratings = ($ratings) ? true : false;    
    $this->trailheads = $this->getTrailheads($this->map, $ratings);

    return array_sum(array_map( fn($a) => count($a), $this->trailheads));
  }

  private function getTrailheads($map = [], $ratings = false) : array {

    $starting_positions = $this->getStartingPositions($map);
    $trailhead          = [];
    $y_boundary         = count($map);
    $x_boundary         = count($map[0]);
    
    for ($current_index = 0; $current_index < count($starting_positions); $current_index++) {
      
      list($y, $x)  = $starting_positions[$current_index];
      $next_index   = $current_index + 1;
      $heap         = new \SplMaxHeap();
      
      $heap->insert([$y, $x, null]);
      
      do {
        list($y, $x, $path) = $heap->extract();
        
        $current_value = $map[$y][$x];        
        $next_value    = $current_value + 1;

        foreach ($this->routes as $route) {
          $next_y = $y + $route[0];
          $next_x = $x + $route[1];

          $within_bounds = (($next_y >= 0 && $next_y < $y_boundary) && ($next_x >= 0 && $next_x < $x_boundary)) ? true : false;
          
          if (!$within_bounds || ($map[$next_y][$next_x] != $next_value) ) {
            continue;
          }          

          if (!isset($trailhead[$next_index])) {
            $trailhead[$next_index] = [];
          }

          $key = md5($next_y . $next_x);          
          if ($ratings) {
            $key = md5($path . $key);
          }

          if (!in_array($key, $trailhead[$next_index]) && $map[$next_y][$next_x] == self::SEQUENCE_END) {
            $trailhead[$next_index][] = $key;           
          } else {
            $heap->insert([$next_y, $next_x, $key]);
          }
        }
      } while ($heap->valid());
    }

    return $trailhead;
  }

  private function getStartingPositions($map = []) : array {
    $positions = [];

    for ($y = 0; $y < count($map); $y++) {
      for ($x = 0; $x < count($map[$y]); $x++) {
        if ($map[$y][$x] == self::SEQUENCE_START) {
          $positions[] = [$y, $x];
        }
      }
    }

    return $positions;
  }
  
  private function processMap($datafile = '') : array {
    $data = [];

    if (file_exists($datafile)) { 
      $data = file_get_contents($datafile);
      $data = explode("\n", $data);
      $data = array_map(fn($a) => str_split($a), $data);

      return $data;
    }

    return [];
  }
}


// ----------------------------------------------------
// MAIN
$map = new Map('input.txt');

print '<p>Q1: The answer is ' . $map->scores() . '</p>';
print '<p>Q2: The answer is ' . $map->scores(true) . '</p>';