<?php

class Map {
  const OBSTACLE        = '#' ;
  const PATH            = '.';
  const POSITION_UP     = '^';
  const POSITION_DOWN   = 'v';
  const POSITION_RIGHT  = '>';
  const POSITION_LEFT   = '<';
  
  public $visited = [];

  private $map;
  private $positions = [];


  public function __construct($data = []) {
    $this->map = $data;

    $this->positions = [
      self::POSITION_UP    => [-1, 0],
      self::POSITION_DOWN  => [1, 0],
      self::POSITION_LEFT  => [0, -1],
      self::POSITION_RIGHT => [0, 1]
    ];
  }

  public function findPosition($map) : array {
    $position = [0, 0];    

    for ($y = 0; $y < count($map); $y++) {
      for ($x = 0; $x < count($map[$y]); $x++) {
        $current = (in_array($map[$y][$x], array_keys($this->positions))) ? true : false;

        if ($current) {            
          return [$y, $x];
        }        
      }
    }

    return $position;
  }

  public function findVisits(): int {
    $next      = [];
    $direction = self::POSITION_UP;

    list($start_y, $start_x) = $this->findPosition($this->map);
    
    $this->visited[$start_y][] = $start_x;

    while (true) {
      $y = $start_y + $this->positions[$direction][0];
      $x = $start_x + $this->positions[$direction][1];

      if (!isset($this->map[$y][$x])) {
        break;
      }

      if ($this->map[$y][$x] == self::OBSTACLE) {
        switch ($direction) {
          case self::POSITION_UP:
            $direction = self::POSITION_RIGHT;
            break;
          case self::POSITION_DOWN:
            $direction = self::POSITION_LEFT;
            break;
          case self::POSITION_LEFT:
            $direction = self::POSITION_UP;
            break;
          case self::POSITION_RIGHT:
            $direction = self::POSITION_DOWN;
            break;
        }

        continue;
      } 

      if (!isset($this->visited[$y])) {
        $this->visited[$y][] = $x;
      } elseif (!in_array($x, $this->visited[$y])) {
        $this->visited[$y][] = $x;
      }

      $start_y = $y;
      $start_x = $x;
    }

    return array_sum(array_map(fn($a) => count($a), $this->visited));
  }

  public function findLoops() : int {
    $loops = 0;
    $map   = $this->map;

    list($start_y, $start_x) = $this->findPosition($map);
    $start_direction = self::POSITION_UP;

    foreach ($this->visited as $y => $row) {
      foreach ($row as $x) {
        $map         = $this->map;      
        $map[$y][$x] = self::OBSTACLE;

        if ($this->checkForLoops($map)) {
          $loops++;
        }        
      }
    }
    
    return $loops;
  }

  private function checkForLoops($map): bool {
    $direction = self::POSITION_UP;    
    $visited   = [];

    foreach ($map as $row => $line) {
      foreach ($line as $col => $icon) {
        if ($icon == $direction) {
          $start_y = $row;
          $start_x = $col;
        }
      }
    }

    if (!isset($start_y) || !isset($start_x)) {
        return false;
    }

    while (true) {
        $y = $start_y + $this->positions[$direction][0];
        $x = $start_x + $this->positions[$direction][1];

        if (isset($visited[$y][$x][$direction])) {
            return true;
        }

        if (!isset($map[$y][$x])) {
            return false;
        }

        if ($map[$y][$x] == self::OBSTACLE) {
          switch ($direction) {
            case self::POSITION_UP:
              $direction = self::POSITION_RIGHT;
              break;
            case self::POSITION_DOWN:
              $direction = self::POSITION_LEFT;
              break;
            case self::POSITION_LEFT:
              $direction = self::POSITION_UP;
              break;
            case self::POSITION_RIGHT:
              $direction = self::POSITION_DOWN;
              break;
          }
            continue;
        }

        $visited[$y][$x][$direction] = true;

        $start_y = $y;
        $start_x = $x;
    }
  }
}

// ----------------------------------------------------------------
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
print '<p>Q1: The first answer is ' . $map->findVisits() . '</p>';
print '<p>Q2: The second answer is ' . $map->findLoops() . '</p>';

