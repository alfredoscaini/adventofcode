<?php 

class Item {
  const POSITION  = 'position';
  const DIRECTION = 'direction';
  const ROUTE     = 'route';

  private $position;
  private $direction;
  private $route;

  
  public function __construct($y = 0, $x = 0, $direction = 0, $route = []) {
    $this->position  = [
      'y' => $y,
      'x' => $x
    ];

    $this->direction = $direction;
    $this->route = $route;
  }

  public function get($item = self::POSITION) : mixed {
    switch ($item) {
      case self::POSITION:
        $data = [$this->position['y'], $this->position['x']];
        break;
      case self::DIRECTION:
        $data = $this->direction;
        break;
      case self::ROUTE:
        $data = $this->route;
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
      case self::DIRECTION:
        $this->direction = $data;
        break;
      case self::ROUTE:
        $this->route = $route;
        break;
    }    
  }
}

class Map {

  const WALL       = '#';
  const MOVE_UP    = '^';
  const MOVE_DOWN  = 'v';
  const MOVE_LEFT  = '<';
  const MOVE_RIGHT = '>';
  const START      = 'S';
  const END        = 'E';
  const PATHS_MAX  = 1000000;
  const MOVE_POINT = 1;
  const TURN_POINT = 1000;

  private $map;
  private $directions;
  private $end;

  private $y_boundary = 0;
  private $x_boundary = 0;

  public $start;


  public function __construct($datafile = '', $double = false) {
    $this->map  = $this->processInput($datafile);

    $this->y_boundary = count($this->map) - 1;
    $this->x_boundary = count($this->map[0]) - 1;


    $this->directions = [
      self::MOVE_UP     => [-1,  0],
      self::MOVE_DOWN   => [ 1,  0],
      self::MOVE_LEFT   => [ 0, -1],
      self::MOVE_RIGHT  => [ 0,  1]
    ];

    list($this->start, $this->end) = $this->findDataPoints($this->map);
  }  

  public function move($y_start = 0, $x_start = 0, $direction = '') : array {
    $map         = $this->map;
    $end_coords  = $this->end->get();
    $best_paths  = [];
		$points      = [];
		$best_path   = self::PATHS_MAX;

		$queue = new \SPLPriorityQueue();
    $queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);

    $data = new Item($y_start, $x_start, $direction, []);

		$queue->insert($data, 0);

    do {
			$current = $queue->extract();

      list($y, $x) = $current['data']->get(Item::POSITION);
      $direction   = $current['data']->get(Item::DIRECTION);
      $route       = $current['data']->get(Item::ROUTE);

			$point = abs($current['priority']);
      $key   = $this->generateKey([$y, $x, $direction]);

      if (!isset($points[$key])) {
        $points[$key] = self::PATHS_MAX;
      }

      $points[$key] = ($point < $points[$key]) ? $point : $points[$key];

			if ($point <= $points[$key]) { 
        if ([$y, $x] == $end_coords) {
          if ($point < $best_path) {
            $best_path    = $point;
            $best_paths[] = $end_coords;
          } elseif ($point == $best_path) {
            $best_paths = array_merge($best_paths, $route);
          }
        }

        list($y_displacement, $x_displacement) = $this->directions[$direction];
        
        $x_next = $x + $x_displacement;
        $y_next = $y + $y_displacement;

        if ($map[$y_next][$x_next] != self::WALL) {
          $data       = new Item($y_next, $x_next, $direction, array_merge($route, [[$y, $x]]));
          $point_next = $point + self::MOVE_POINT; 

          $queue->insert($data, -$point_next);
        }

        foreach ($this->directions as $key => $value) {
          if ($key !== $direction) {
            $data       = new Item($y, $x, $key, $route);
            $point_next = $point + self::TURN_POINT;

            $queue->insert($data, -$point_next);
          }
        }
      } 
		} while ($queue->valid());

		return [$best_path, array_unique($best_paths, SORT_REGULAR)];
  }

  private function findDataPoints($map = []) : array {
    $start = [];
    $end   = [];
    $walls = [];

    for ($y = 0; $y < $this->y_boundary; $y++) {
      for ($x = 0; $x < $this->x_boundary; $x++) {
        switch ($map[$y][$x]) {
          case self::START:
            $start = new Item($y, $x, '', []);
            break;

          case self::END:
            $end = new Item($y, $x, '', []);
            break;
        }
      }
    }

    return [$start, $end];
  }

  private function generateKey($data = []) : string {
    return md5(join('~', $data));
  }

  private function processInput($datafile = '') : array {
    if (file_exists($datafile)) {
      $data = explode("\n", file_get_contents($datafile));
    }
  
    foreach ($data as $line) {
      $map[] = str_split($line);
    }

    return $map;
  }
}


// -------------------------------------------------------
// Main
$map = new Map('input.txt', false);
list($y, $x) = $map->start->get(Item::POSITION);
    
list($count, $best_path) = $map->move($y, $x, Map::MOVE_RIGHT);
print '<p>Q1: The answer is ' . $count . '</p>';
print '<p>Q2: The answer is ' . count($best_path) . '</p>';