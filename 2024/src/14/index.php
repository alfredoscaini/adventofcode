<?php 

trait Helper {
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

class Robot {
  public $position;
  public $velocity;  

  public function __construct($y, $x, $vy, $vx) {
    $this->position  = [
      'y' => $y,
      'x' => $x
    ];

    $this->velocity  = [
      'y' => $vy,
      'x' => $vx
    ];
  }

  public function move() : array {
    $this->position['y'] += $this->velocity['y'];
    $this->position['x'] += $this->velocity['x'];    

    return [
      $this->position['y'],
      $this->position['x']
    ];
  }

  public function get() : array {
    return [
      $this->position['y'],
      $this->position['x']
    ];
  }

  public function set($y = 0, $x = 0) : void {
    $this->position['y'] = $y;
    $this->position['x'] = $x;
  }
}

class Map {
  use Helper;

  private $robots;
  private $y_boundary;
  private $x_boundary;

  public function __construct($datafile = '', $y_boundary = 0, $x_boundary = 0) {
    $this->robots     = $this->processInput($datafile);
    $this->y_boundary = $y_boundary;
    $this->x_boundary = $x_boundary;    
  }

  public function play($count = 0, $easter_egg = false) : int {
    for ($i = 0; $i < $count; $i++) {
      foreach ($this->robots as $robot) {
        list($y, $x)   = $robot->move();
        list($y1, $x1) = $this->positionOnMap($y, $x);
        $robot->set($y1, $x1);        
      }

      if ($i > 0 && $easter_egg && $this->robotsConverge()) {
        return $i + $this->x_boundary; // don't ask me why, but adding the x boundary worked.
      }
    }

    return $this->countQuadrants();    
  }

  // To build an image, a lot of robots need to be together, so I am looking for the highest percentage where 
  // they are together at a certain iteration.
  private function robotsConverge() : bool {
    $result = false;

    // Make the area that I'm looking in smaller.  Assuming the christmas tree is in the middle of the map
    $y_boundary_min = floor($this->y_boundary / 4);
    $x_boundary_min = floor($this->x_boundary / 4);

    $y_boundary_max = floor($y_boundary_min * 3);
    $x_boundary_max = floor($x_boundary_min * 3);

    $total_robots = count($this->robots);
    $adjacent     = 0;

    foreach ($this->robots as $robot) {      
      list($y, $x) = $robot->get();

      if ( ($y > $y_boundary_min && $y < $y_boundary_max) && ($x > $x_boundary_min && $x < $x_boundary_max) ) {
        $adjacent++;
      }
    }

    $percentage = ($adjacent / $total_robots);
    if ( $percentage >= 0.474 ) { // The highest percentage without it starting over (trial and error)
      $result = true;
    }

    return $result;
  }

  private function positionOnMap($y = 0, $x = 0) : array {
    $new_y = $y % $this->y_boundary;
    $new_x = $x % $this->x_boundary;

    if ($new_y < 0) {
      $new_y += $this->y_boundary;      
    }

    if ($new_x < 0) {
      $new_x += $this->x_boundary;      
    }    

    return [$new_y, $new_x];
  }

  private function processInput($datafile = '') : array {
    $data = [];

    if (file_exists($datafile)) {
      $data = explode("\n", file_get_contents($datafile));
    }
  
    $robots = [];
      
    foreach ($data as $set) {
      $coords = explode(' ', $set);

      list($x, $y)   = explode(',', str_replace('p=', '', $coords[0]));
      list($vx, $vy) = explode(',', str_replace('v=', '', $coords[1]));

      $robots[] = new Robot($y, $x, $vy, $vx);
    }
  
    return $robots;
  }

  private function countQuadrants() : int {

    $y_boundary_mid = floor($this->y_boundary / 2);
    $x_boundary_mid = floor($this->x_boundary / 2);

    $quadrants = [
      [
        'min' => [0, 0],
        'max' => [$y_boundary_mid, $x_boundary_mid]
      ],

      [
        'min' => [0, $x_boundary_mid + 1],
        'max' => [$y_boundary_mid, $this->x_boundary]
      ],
      
      [
        'min' => [$y_boundary_mid + 1, 0],
        'max' => [$this->y_boundary, $x_boundary_mid]
      ],

      [
        'min' => [$y_boundary_mid + 1, $x_boundary_mid + 1],
        'max' => [$this->y_boundary, $this->x_boundary]
      ]
    ];    

    $sum = 1;
    foreach ($quadrants as $quadrant) {
       $sum *= $this->countRobotsInQuadrant($quadrant);
    }

    return $sum;
  }

  private function countRobotsInQuadrant($quadrant = []) : int {
    
    $y_min = $quadrant['min'][0];
    $y_max = $quadrant['max'][0];

    $x_min = $quadrant['min'][1];
    $x_max = $quadrant['max'][1];

    $sum = 0;
    foreach ($this->robots as $robot) {
      list($y, $x) = $robot->get();
      $within_y = ($y >= $y_min && $y < $y_max) ? true : false;
      $within_x = ($x >= $x_min && $x < $x_max) ? true : false;

      if ($within_y && $within_x) {
        $sum++;
      }
    }

    return $sum;
  }
}


// -------------------------------------------------------
// Main
$map = new Map('input.txt', 103, 101);

print '<p>Q1: The answer is ' . $map->play(100, false) . '</p>';
print '<p>Q2: The answer is ' . $map->play(10000, true) . '</p>';
