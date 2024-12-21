<?php 

class Item {
  const POSITION  = 'position';
  const STEPS = 'steps';

  private $position;
  private $steps;

  
  public function __construct($y = 0, $x = 0, $steps = 0) {
    $this->position  = [
      'y' => $y,
      'x' => $x
    ];

    $this->steps = $steps;
  }

  public function get($item = self::POSITION) : mixed {
    switch ($item) {
      case self::POSITION:
        $data = [$this->position['y'], $this->position['x']];
        break;
      case self::STEPS:
        $data = $this->steps;
        break;
    }
    return $data;
  }

  public function set($item = self::POSITION, $data = []) : void {
    switch ($item) {
      case self::POSITION:
        $this->position['y'] = $data[0];
        $this->position['x'] = $data[1];
        break;
      case self::STEPS:
        $this->steps = $data;
        break;
    }    
  }
}

class Map {

  const WALL       = '#';
  const SPACE      = '.';
  const MOVE_UP    = '^';
  const MOVE_DOWN  = 'v';
  const MOVE_LEFT  = '<';
  const MOVE_RIGHT = '>';
  const LIMIT      = 1024;
  const MAX        = 1000000;

  private $map;
  private $walls;
  private $directions;
  private $end;

  private $y_boundary = 70;
  private $x_boundary = 70;

  public $start;


  public function __construct($datafile = '') {
    list($this->map, $this->walls) = $this->processInput($datafile);

    $this->directions = [
      self::MOVE_UP     => [-1,  0],
      self::MOVE_DOWN   => [ 1,  0],
      self::MOVE_LEFT   => [ 0, -1],
      self::MOVE_RIGHT  => [ 0,  1]
    ];

    $this->start = new Item(0, 0, self::MOVE_RIGHT);
    $this->end   = new Item($this->y_boundary, $this->x_boundary);
  }

  public function getWalls() : array {
    return $this->walls;
  }

  public function move($y_start = 0, $x_start = 0, $limit = self::LIMIT) : int {
    $map         = $this->map;
    $walls       = $this->walls;
    $end_coords  = $this->end->get();
    $repeats     = [];
    $steps       = self::MAX;

		$queue = new \SPLQueue();
    $data  = new Item($y_start, $x_start, 0);

    // Ugh, part 2 ...
    $this->resetMap($map, $limit);

		$queue->enqueue($data);

    do {
			$current = $queue->dequeue();

      list($y, $x)  = $current->get(Item::POSITION);
      $current_step = $current->get(Item::STEPS);

      if ([$y, $x] == $end_coords) {
        $steps = min($steps, $current_step);
        break;
      }

      foreach ($this->directions as $direction => $coords) {
        list($y_displacement, $x_displacement) = $this->directions[$direction];
          
        $x_next = $x + $x_displacement;
        $y_next = $y + $y_displacement;

        $key = $y_next . '-' . $x_next;

        $within_bounds = (isset($map[$y_next][$x_next]))                           ? true : false;
        $is_wall       = ($within_bounds && $map[$y_next][$x_next] == self::WALL)  ? true : false;
        $repeated      = (array_key_exists($key, $repeats))                        ? true : false;

        if ($within_bounds && !$is_wall && !$repeated) {
          $data = new Item($y_next, $x_next, ($current_step + 1));
          $queue->enqueue($data);

          $repeats[$key] = $current_step;
        }
      }
		} while (!$queue->isEmpty());

		return $steps;
  }

  private function resetMap(&$map, $limit) {
    $walls = $this->walls;

    for ($y = 0; $y <= $this->y_boundary; $y++) {
      for ($x = 0; $x <= $this->x_boundary; $x++) {        
        $map[$y][$x] = self::SPACE;
      }
    }

    for($i = 0; $i < count($walls); $i++) {
      list($y, $x) = $walls[$i];
      if ($i < $limit) {
        $map[$y][$x] = self::WALL;
      }
    }
  }

  private function findDataPoints($map = []) : array {
    $start = [];
    $end   = [];
    $walls = [];

    for ($y = 0; $y < $this->y_boundary; $y++) {
      for ($x = 0; $x < $this->x_boundary; $x++) {
        switch ($map[$y][$x]) {
          case self::START:
            $start = new Item($y, $x, '');
            break;

          case self::END:
            $end = new Item($y, $x, '');
            break;
        }
      }
    }

    return [$start, $end];
  }

  private function processInput($datafile = '') : array {
    $map   = [];
    $walls = [];

    if (file_exists($datafile)) {
      $data = explode("\n", file_get_contents($datafile));      
    }

    for ($y = 0; $y <= $this->y_boundary; $y++) {
      for ($x = 0; $x <= $this->x_boundary; $x++) {        
        $map[$y][$x] = self::SPACE;
      }
    }

    foreach ($data as $index => $line) {
      list($x, $y) = explode(",", $line);

      $walls[] = [$y, $x];
      
      if ($index < self::LIMIT) {
        $map[$y][$x] = self::WALL;
      }
    }

    return [$map, $walls];
  }
}


// -------------------------------------------------------
// Main
$map = new Map('input.txt', false);
list($y, $x) = $map->start->get(Item::POSITION);
    
$count = $map->move($y, $x);
print '<p>Q1: The answer is ' . $count . '</p>';

$walls  = $map->getWalls();
$coords = [];

for ($limit = 0; $limit < count($walls); $limit++) {
  list($y, $x) = $walls[$limit];

  $count = $map->move(0, 0, $limit);
  
  if ($count === Map::MAX) {
    list($y, $x) = $walls[$limit - 1];
    break;
  }
}

print '<p>Q2: The answer is ' . $x . ',' . $y . '</p>';