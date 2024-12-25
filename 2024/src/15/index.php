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

class Icon {
  private $position;
  public $icon;
  
  public function __construct($y, $x, $icon = '') {
    $this->position  = [
      'y' => $y,
      'x' => $x
    ];

    $this->icon = $icon;
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

  const BOX        = 'O';
  const WALL       = '#';
  const MOVE_UP    = '^';
  const MOVE_DOWN  = 'v';
  const MOVE_LEFT  = '<';
  const MOVE_RIGHT = '>';
  const ROBOT      = '@';
  const SPACE      = '.';
  const BOX_LEFT   = '[';
  const BOX_RIGHT  = ']';

  const DISTANCE   = 100;


  private $map;
  private $moves;
  private $boxes;
  private $robot;
  private $walls;
  private $y_boundary;
  private $x_boundary;
  private $directions;
  private $double_up;

  private $y_map_boundary = 0;
  private $x_map_boundary = 0;

  public function __construct($datafile = '', $double = false) {
    $this->double_up = ($double) ? true : false;
    list($this->map, $this->moves)  = $this->processInput($datafile, $this->double_up);

    $this->y_map_boundary = count($this->map);
    $this->x_map_boundary = count($this->map[0]);


    $this->directions = [
      self::MOVE_UP     => [-1,  0],
      self::MOVE_DOWN   => [ 1,  0],
      self::MOVE_LEFT   => [ 0, -1],
      self::MOVE_RIGHT  => [ 0,  1]
    ];

    list($this->robot, $this->boxes, $this->walls) = $this->findBoxesWallsAndRobot($this->map);
  }

  public function sumBoxes() : int {
    $sum = 0;
    
    foreach ($this->boxes as $box) {
      if ($this->double_up) {
        $box = $box[0];
      }

      $sum += $this->findDistance($box);
    }
    
    return $sum;
  }

  public function move() : void {
    foreach ($this->moves as $direction) {      
      list($y, $x) = $this->robot->get();

      if (array_key_exists($direction, $this->directions)) {
        $y += $this->directions[$direction][0];
        $x += $this->directions[$direction][1];
      
        if ($this->movementAllowed($y, $x, $direction)) {
          $this->robot->set($y, $x);          
        }
      }
    }
  }

  private function movementAllowed($y = 0, $x = 0, $direction = []) : bool {

    if (!isset($this->map[$y][$x])) {
      return false;
    }
    
    $direction_coords = $this->directions[$direction];

    $movement_allowed = true;
    $y_displacement   = $direction_coords[0];
    $x_displacement   = $direction_coords[1];

    foreach($this->walls as $wall) {
      list($y_wall, $x_wall) = $wall->get();
      if ($y_wall == $y && $x_wall == $x) {
        $movement_allowed = false;
      }
    }

    if ($movement_allowed) {   
      if ($this->double_up) {
        $partial_box_move = false;
        $tmp = [];

        foreach ($this->boxes as $key => $box) {
          $left  = $box[0];
          $right = $box[1];

          list($y_left, $x_left)   = $left->get();
          list($y_right, $x_right) = $right->get();
          
          if ( ($y_left == $y && $x_left == $x) || ($y_right == $y && $x_right == $x) ) {    
            
            $partial_box_move = (($y_left == $y && $x_left == $x) && ($y_right == $y && $x_right == $x)) ? false : true;
          
            $y_left_box  = $y_left + $y_displacement;
            $x_left_box  = $x_left + $x_displacement;
            $y_right_box = $y_right + $y_displacement;
            $x_right_box = $x_right + $x_displacement;

            $movement_allowed = false;

            if ($direction == self::MOVE_LEFT) {
              for ($x = $x_left; $x > 0; $x--) {
                if ($this->map[$y_left][$x] == self::SPACE) {
                  $movement_allowed = true;
                  break;
                }
              }
            }

            if ($direction == self::MOVE_RIGHT) {
              for ($x = $x_right; $x < 100; $x++) {
                if ($this->map[$y_right][$x] == self::SPACE) {
                  $movement_allowed = true;
                  break;
                }
              }
            }

            if ($direction == self::MOVE_UP) {
              for ($y = $y_left, $y1 = $y_right; $y > 0, $y1 > 0; $y--, $y1--) {
                if ($this->map[$y][$x_left] == self::SPACE && $this->map[$y1][$x_right] == self::SPACE) {
                  $movement_allowed = true;
                  break;
                }
              }
            }

            if ($direction == self::MOVE_DOWN) {
              for ($y = $y_left, $y1 = $y_right; $y < 50, $y1 < 50; $y++, $y1++) {
                if ($this->map[$y][$x_left] == self::SPACE && $this->map[$y1][$x_right] == self::SPACE) {
                  $movement_allowed = true;
                  break;
                }
              }
            }

            if ($movement_allowed) {
              if ($partial_box_move) {
                $tmp[$key] = [$y_left_box, $x_left_box, $y_right_box, $x_right_box];
              } else {
                $this->boxes[$key][0]->set($y_left_box, $x_left_box);
                $this->boxes[$key][1]->set($y_right_box, $x_right_box);
                break;
              }              
            }
          }     
        }  
        
        if (count($tmp)) {
          foreach($tmp as $key => $coords) {
            $this->boxes[$key][0]->set($coords[0], $coords[1]);
            $this->boxes[$key][1]->set($coords[2], $coords[3]);
          }
        }
      } else {        
        foreach ($this->boxes as $box) {
          list($box_y, $box_x) = $box->get();
          if ($box_y == $y && $box_x == $x) {

            $next_y = $box_y + $y_displacement;
            $next_x = $box_x + $x_displacement;

            $movement_allowed = $this->movementAllowed($next_y, $next_x, $direction);
            
            if ($movement_allowed) {
              $box->set($next_y, $next_x);            
            }

            break;
          }
        }
      }
    }

    return $movement_allowed;
  }

  private function findBlock($y = 0, $x = 0, $direction = null) : array {
    list($y_displacement, $x_displacement) = $this->directions[$direction];

    $y_next_block = (($y + $y_displacement) >= 0) ? ($y + $y_displacement) : 0;
    $x_next_block = (($x + $x_displacement) >= 0) ? ($x + $x_displacement) : 1; 

    
    if (isset($this->map[$y_next_block][$x_next_block])) {
      if ($this->map[$y_next_block][$x_next_block] == self::WALL) {
        return [$y_next_block, $x_next_block];
      } else {
        return $this->findBlock($y_next_block, $x_next_block, $direction);
      }
    }
  }

  private function findDistance($box = null) : int {
    list($box_y, $box_x) = $box->get();

    return (self::DISTANCE * $box_y)  + $box_x;
  }
  
  private function findBoxesWallsAndRobot($map = []) : array {
    $boxes = [];
    $robot = [];
    $walls = [];

    $tmp_boxes = [];
    $counter   = 1;

    for ($y = 0; $y < $this->y_map_boundary; $y++) {
      for ($x = 0; $x < $this->x_map_boundary; $x++) {
        switch ($map[$y][$x]) {
          case self::BOX:
            $boxes[] = new Icon($y, $x, self::BOX);
            break;

          case self::WALL:
            $walls[] = new Icon($y, $x, self::WALL);
            break;

          case self::ROBOT:
            $robot = new Icon($y, $x, self::ROBOT);
            break;
            
          case self::BOX_LEFT:
            $tmp_boxes[] = new Icon($y, $x, self::BOX_LEFT);
            break;

          case self::BOX_RIGHT:
            $tmp_boxes[] = new Icon($y, $x, self::BOX_RIGHT);
            break;
        }

        if (count($tmp_boxes) == 2) {
          $boxes['box-' . $counter++] = [
            $tmp_boxes[0],
            $tmp_boxes[1]
          ];

          $tmp_boxes = [];
        }
      }
    }

    return [$robot, $boxes, $walls];
  }

  private function processInput($datafile = '', $double = false) : array {
    if (file_exists($datafile)) {
      $data = explode("\n", file_get_contents($datafile));
    }
  
    $tmp     = [];
    $counter = 0;
    foreach ($data as $set) {
      if (empty($set)) {
        $counter++;
      }

      $tmp[$counter][] = $set;
    }

    foreach ($tmp[0] as $line) {
      $data = str_split($line);

      if ($double) {        
        $data = [];
        foreach (str_split($line) as $icon) {
          if (in_array($icon, [self::WALL, self::SPACE])) {
            $data[] = $icon;
            $data[] = $icon;
          } elseif ($icon == self::BOX) {
            $data[] = self::BOX_LEFT;
            $data[] = self::BOX_RIGHT;
          } else {
            $data[] = self::ROBOT;
            $data[] = self::SPACE;
          }
        }
      }

      $map[] = $data;
    }

    $moves = str_split(implode('', $tmp[1]));

    return [$map, $moves];
  }
}


// -------------------------------------------------------
// Main


$map = new Map('input.txt', false);
$map->move();
print '<p>Q1: The answer is ' . $map->sumBoxes() . '</p>';


$map = new Map('input.txt', true);
$map->move();
print '<p>Q2: The answer is ' .  $map->sumBoxes() . '</p>';