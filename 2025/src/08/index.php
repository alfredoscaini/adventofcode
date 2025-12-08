<?php

class Map {
  private $data;
  private $distances;

  public function __construct($file) {
    $this->data = $this->process($file);
    $this->distances = $this->calculateDistances();
  }

  private function process(string $file) : array {
    $data = file_get_contents($file);
    $data = explode("\n", $data);

    for($i = 0; $i < count($data); $i++) {
      $data[$i] = explode(',', $data[$i]);
    }

    return $data;
  }

  public function getData() : array {
    return $this->data;
  }

  public function getDistances() : array {
    return $this->distances;
  }

  private function calculateDistances() : array {
    $map   = $this->data;
    $pairs = [];

    for ($i = 0 ; $i < count($map); $i++) {
      $coords_current = $map[$i];

      for ($j = 1; $j < count($map); $j++) {
        if ($i == $j) { continue; }

        $coords_next    = $map[$j];

        $distance_x = $coords_next[0] - $coords_current[0];
        $distance_y = $coords_next[1] - $coords_current[1];
        $distance_z = $coords_next[2] - $coords_current[2];

        $distance = ($distance_x ** 2) + ($distance_y ** 2) + ($distance_z ** 2);
        
        $key        = $i . '-' . $j;
        $mirror_key = $j . '-' . $i;

        if (!array_key_exists($key, $pairs) && !array_key_exists($mirror_key, $pairs)) {
          $pairs[$key] = [
            'distance' => $distance, 
            'box1'     => $i, 
            'box2'     => $j,
            'coords1'  => $coords_current,
            'coords2'  => $coords_next
          ];
        }
      }
    }

    uasort($pairs, function($a, $b) {
      return $a['distance'] <=> $b['distance'];
    });
    
    return $pairs;
  }

  public function calculateConnections($count = 0) : array {
    $circuits    = [];
    $connections = $this->getDistances();
    $index       = 0;

    foreach ($connections as $key => $connection) {
      if ($index < $count) {
        $index++;
            
        $junction_left  = $connection['box1'];
        $junction_right = $connection['box2'];

        $connected     = [[$junction_left, $junction_right]];
        $not_connected = [];

        foreach ($circuits as $circuit) {
          if (in_array($junction_left, $circuit) || in_array($junction_right, $circuit)) {
            $connected[] = $circuit;
          } else {
            $not_connected[] = $circuit;
          }
        }

        $connected = array_reduce($connected, function($carry, $item) {
          return array_merge($carry, is_array($item) ? $item : [$item]);
        }, []);
        
        $expanded_circuit = array_merge([$junction_left, $junction_right], $connected);
        $not_connected[]  = array_values(array_unique($expanded_circuit));
        $circuits         = $not_connected;
      }
    }

    usort($circuits, function($a, $b) {
      return count($b) - count($a);
    });

    return $circuits;
  }

  public function continousCircuit($count) : array {
    $circuits    = [];
    $connections = $this->getDistances();

    foreach ($connections as $key => $connection) {
      $junction_left  = $connection['box1'];
      $junction_right = $connection['box2'];

      $connected     = [[$junction_left, $junction_right]];
      $not_connected = [];

      foreach ($circuits as $circuit) {
        if (in_array($junction_left, $circuit) || in_array($junction_right, $circuit)) {
          $connected[] = $circuit;
        } else {
          $not_connected[] = $circuit;
        }
      }

      $connected = array_reduce($connected, function($carry, $item) {
        return array_merge($carry, is_array($item) ? $item : [$item]);
      }, []);
      
      $expanded_circuit = array_merge([$junction_left, $junction_right], $connected);
      $not_connected[]  = array_values(array_unique($expanded_circuit));
      $circuits         = $not_connected;

      if (count($circuits) == 1 && count($circuits[0]) == $count) {
        return [$junction_left, $junction_right];
      }
    }

    return [0, 0];
  }
}

$map  = new Map('./input.txt');

$connections = $map->calculateConnections(1000);

$sum = 1;
for ($i = 0; $i <= 2; $i++) {
  $sum *= count($connections[$i]);
}

$continous_circuit = $map->continousCircuit(1000);
$grid = $map->getData();

$position_x1 = $grid[$continous_circuit[0]][0];
$position_x2 = $grid[$continous_circuit[1]][0];

print '<p>The product of the 3 largest circuits is ' . $sum . '</p>';
print '<p>The product of the two circuit X positions is ' . ($position_x1 * $position_x2) . '</p>';
