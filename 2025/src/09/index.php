<?php

class Map {
  private $data;
  private $rectangles;
  private $boundaries = [];

  public function __construct($file) {
    $this->data       = $this->process($file);
    $this->rectangles = $this->calculateRectangles();
    $this->boundaries  = $this->calculateBoundaries();
  }

  private function process(string $file) : array {
    $data = file_get_contents($file);
    $data = explode("\n", $data);

    for ($i = 0; $i < count($data); $i++) {
      $coords   = explode(',', $data[$i]);
      $data[$i] = [
        'x' => $coords[0],
        'y' => $coords[1]
      ];
    }

    uasort($data, function ($a, $b) {
      return $a['y'] <=> $b['y'];
    });

    return $data;
  }

  private function calculateBoundaries() : array {
    $map        = $this->getData();
    $boundaries = [];
   
    for ($i = 0; $i < count($map); $i++) {
      $y = $map[$i]['y'];

      for ($j = ($i + 1); $j < count($map); $j++) {

        if ($map[$i]['y'] == $map[$j]['y']) {
          $min = min($map[$i]['x'], $map[$j]['x']);
          $max = max($map[$i]['x'], $map[$j]['x']);

          $boundaries[$y] = ['x_min' => $min, 'x_max' => $max];
        }       
      }
    }

    
    for ($i = 0; $i < count($map); $i++) {

      for ($j = ($i + 1); $j < count($map); $j++) {

        if ($map[$i]['x'] == $map[$j]['x']) {
          $min = min($map[$i]['y'], $map[$j]['y']);
          $max = max($map[$i]['y'], $map[$j]['y']);

          for ($k = $min + 1; $k < $max; $k++) {
            if (array_key_exists($k, $boundaries)) {
              $min_x = min($boundaries[$k]['x_min'], $map[$i]['x']);
              $max_x = max($boundaries[$k]['x_max'], $map[$i]['x']);

              $boundaries[$k] = ['x_min' => $min_x, 'x_max' => $max_x];
            } else {
              $boundaries[$k] = ['x_min' => $map[$i]['x'], 'x_max' => $map[$i]['x']];
            }
          }
        }
      }
    }

    ksort($boundaries);

    return $boundaries;

  }

  private function calculateRectangles() : array {
    $coords = $this->getData();
    $data   = [];

    for ($i = 0; $i < count($coords); $i++) {
      for ($j = ($i + 1); $j < count($coords); $j++) {
        $position_x1 = $coords[$i]['x'];
        $position_y1 = $coords[$i]['y'];
        $position_x2 = $coords[$j]['x'];
        $position_y2 = $coords[$j]['y'];

        if ($position_x1 == $position_x2 || $position_y1 == $position_y2) {
          continue; // they lie on the same row, or the same column
        }

        $side_a = abs($position_x1 - $position_x2);
        $side_b = abs($position_y1 - $position_y2);

        if ($side_a == $side_b) {
          continue; // we have a square
        }

        $area = ($side_a + 1) * ($side_b + 1);

        $x_min = min($position_x1, $position_x2);
        $x_max = max($position_x1, $position_x2);
        $y_min = min($position_y1, $position_y2);
        $y_max = max($position_y1, $position_y2);

        $data[] = [
          'area'   => $area,
          'coords' => ['y_min' => $y_min, 'y_max' => $y_max, 'x_min' => $x_min, 'x_max' => $x_max]
        ];
      }
    }

    uasort($data, function ($a, $b) {
      return $b['area'] <=> $a['area']; 
    });

    return array_values($data);
  }

  public function getData() : array {
    return $this->data;
  }

  public function getRectangles() : array {
    return $this->rectangles;
  }

  public function getBoundaries() : array {
    return $this->boundaries;
  }

  public function withinBoundary($coords, $boundary) : bool {
    $x_min = $coords['x_min'];
    $x_max = $coords['x_max'];
    $y_min = $coords['y_min'];
    $y_max = $coords['y_max'];

    $found = true;
    for ($y = $y_min; $y <= $y_max; $y++) {
      if ($found) {
        if (!array_key_exists($y, $boundary)) {
          $found = false;
          continue;
        }
        
        if ($x_min <= $boundary[$y]['x_min'] || $x_max >= $boundary[$y]['x_max']) {
          $found = false;
        }
      }
    }

    return $found;
  }
}

$map  = new Map('./input.txt');

$rectangles = $map->getRectangles();
$largest_rectangle = $rectangles[0];

print '<p>The largest rectangle is ' . $largest_rectangle['area'] . '</p>';

$boundaries = $map->getBoundaries();
$area = 0;

foreach ($rectangles as $rectangle) {  
  if ($area == 0 && $map->withinBoundary($rectangle['coords'], $boundaries) ) {
    $area = $rectangle['area'];
  }
}

print '<p>The largest rectangle within the boundaries is ' . $area . '</p>';