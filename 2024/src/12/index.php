<?php

class Map {
  const MOVE_UP    = '^';
  const MOVE_DOWN  = 'v';
  const MOVE_LEFT  = '<';
  const MOVE_RIGHT = '>';
  
  const MOVE_UP_RIGHT   = '^>';
  const MOVE_UP_LEFT    = '^<';
  const MOVE_DOWN_RIGHT = 'v>';
  const MOVE_DOWN_LEFT  = 'v<';

  private $map;
  private $directions;
  private $repeated;
  private $fencing;
  private $y_boundary;
  private $x_boundary;

  public function __construct($datafile = '') {
    $this->map = $this->processInput($datafile);
    
    $this->repeated   = [];
    $this->fencing    = [];
    $this->y_boundary = count($this->map);
    $this->x_boundary = count($this->map[0]);


    $this->directions = [
      self::MOVE_UP     => [-1,  0],
      self::MOVE_DOWN   => [ 1,  0],
      self::MOVE_LEFT   => [ 0, -1],
      self::MOVE_RIGHT  => [ 0,  1]
    ];

    $this->fencing = [
      self::MOVE_UP_RIGHT   => [-1,  1],
      self::MOVE_UP_LEFT    => [-1, -1],
      self::MOVE_DOWN_LEFT  => [ 1, -1],
      self::MOVE_DOWN_RIGHT => [ 1,  1]
    ];
  }

  public function calculate($fencing = false) : int {
    $data  = [];
    $index = 0;

    $this->repeated = []; // initialize to empty

    foreach ($this->map as $y => $row) {
      foreach ($row as $x => $character) {
        

        if (!isset($this->repeated[$y][$x])) {
          $data[++$index] = [
            'area'      => 0,
            'perimeter' => 0,
            'character' => $character,
            'borders'   => []
          ];

          $this->queue([$y, $x], $index, $fencing, $data[$index]);
        }
      }
    }

    

    if ($fencing) {
      foreach ($data as $key => $item) {
        $data[$key]['perimeter'] = $this->calculateFencing($item['borders']);
      }
    }

    $sum = 0;
    foreach ($data as $key => $item) {
      $sum += $item['area'] * $item['perimeter'];
    }
    
    return $sum;
  }

  private function calculateFencing($borders) : int {
    $perimeter = 0;

    foreach ($borders as $y => $row) {
      foreach ($row as $x => $directions) {
        $perimeter += $this->adjustPerimeter($directions);
      }
    }

    return $perimeter;
  }

  private function adjustPerimeter($directions = []) : int {
    $perimeter = 0;

    $up    = isset($directions[self::MOVE_UP])    ? true : false;
    $down  = isset($directions[self::MOVE_DOWN])  ? true : false;
    $left  = isset($directions[self::MOVE_LEFT])  ? true : false;
    $right = isset($directions[self::MOVE_RIGHT]) ? true : false;
    

    if ($up && $left) {
      $perimeter++;
    } elseif (!$up && !$left && isset($directions[self::MOVE_UP_LEFT])) {
      $perimeter++;
    }

    if ($up && $right) {
      $perimeter++;
    } elseif (!$up && !$right && isset($directions[self::MOVE_UP_RIGHT])) {
      $perimeter++;
    }
    
    if ($down && $left) {
      $perimeter++;
    } elseif (!$down && !$left && isset($directions[self::MOVE_DOWN_LEFT])) {
      $perimeter++;
    }
    
    if ($down && $right) {
      $perimeter++;
    } elseif (!$down && !$right && isset($directions[self::MOVE_DOWN_RIGHT])) {
      $perimeter++;
    }

    return $perimeter;
  }

  private function queue($coords, $index, $fencing, &$data) {
    $queue = new SplQueue();
    $queue->enqueue($coords);

    do {
      list($y, $x) = $queue->dequeue();
      
      if (!isset($this->repeated[$y][$x])) {
        $this->repeated[$y][$x] = $index;

        $data['area']++;

        foreach ($this->directions as $direction => $displacement) {
          $y_next = $y + $displacement[0];
          $x_next = $x + $displacement[1];
          
          $exists   = (isset($this->map[$y_next][$x_next]))                           ? true : false;
          $is_match = ($exists && $this->map[$y_next][$x_next] == $data['character']) ? true : false;

          if ($is_match) {
            $queue->enqueue([$y_next, $x_next]);
          } else {
            if ($fencing) {
              $data['borders'][$y][$x][$direction] = 1;
            } else {
              $data['perimeter']++;
            }
          }        
        }

        if ($fencing) {
          foreach ($this->fencing as $direction => $displacement) {
            $y_next = $y + $displacement[0];
            $x_next = $x + $displacement[1];
            
            $exists   = (isset($this->map[$y_next][$x_next]))                           ? true : false;
            $is_match = ($exists && $this->map[$y_next][$x_next] == $data['character']) ? true : false;
  
            if (!$is_match) {
              $data['borders'][$y][$x][$direction] = 1;
            }        
          }
        }
      }
    } while ($queue->count());
  }

  private function processInput($datafile) : array {
    $data = [];

    if (file_exists($datafile)) {
      $data = explode("\n", file_get_contents($datafile));
      $data = array_map(fn($a) => str_split($a), $data);
    }

    return $data;
  }
}


$map = new Map('input.txt');
print '<p>Q1: The answer is ' . $map->calculate() . '</p>';
print '<p>Q2: The answer is ' . $map->calculate(true) . '</p>';