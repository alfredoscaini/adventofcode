<?php

class Map {
  private $data;
  public const SPLITTER = '^';
  public const SPACE    = '.';
  public const START    = 'S';

  public function __construct($file) {
    $this->data = $this->process($file);
  }

  private function process(string $file) : array {
    $data = file_get_contents($file);
    $data = explode("\n", $data);

    for($i = 0; $i < count($data); $i++) {
      $data[$i] = str_split($data[$i]);
    }

    return $data;
  }

  public function transverse() : array {
    $map      = $this->data;
    $beam     = [array_search(self::START, $map[0])];
    $split    = 0;

    $timelines = $map[0];    
    array_walk($timelines, function (&$value, $index) {
      $value = ($value == self::START) ? 1 : 0 ;
    });

    foreach ($map as $row) {
      for ($i = 0; $i < count($row); $i++) {
        if ($row[$i] == self::SPLITTER && in_array($i, $beam)) {
          $split++;

          unset($beam[array_search($i, $beam)]);
          $beam = array_values($beam);

          if ($i > 0 && $i < count($row) - 1) {
            $left  = $i - 1;
            $right = $i + 1;

            if (!in_array($left, $beam)) {
              $beam[] = $left;
            }

            if (!in_array($right, $beam)) {
              $beam[] = $right;
            } 
            
            $timelines[$left]  += $timelines[$i];
            $timelines[$right] += $timelines[$i];
            $timelines[$i]      = 0;
          }
        }
      }
    }

    return [$split, array_sum($timelines)];
  }
}

$map  = new Map('./input.txt');
list($split, $timelines) = $map->transverse();

print '<p>Q1: The number of times the beam is split is ' . $split . '</p>';
print '<p>Q1: The number of alternate timelines are ' . $timelines . '</p>';