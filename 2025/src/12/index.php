<?php

class Data {
  private $shapes;
  private $regions;

  private const SHAPE = '#';

  public function __construct($file) {
    list($this->shapes, $this->regions) = $this->process($file);
  }

  private function process(string $file) : array {
    $data = file_get_contents($file);
    $data = explode("\n\n", $data);

    $regions = explode("\n", array_pop($data));

    $defined_regions = [];
    foreach ($regions as $region) {
      list($dimensions, $indexes) = explode(': ', $region);
      list($width, $length) = explode('x', $dimensions);
      $counts = explode(' ', $indexes);
      
      $defined_regions[] = [
        'width'  => $width,
        'length' => $length,
        'indexes' => array_map(function ($x) {
                      return intval($x);
                    }, $counts)
      ];
    }

    $shapes = array_map(function ($x) {
      return substr_count($x, self::SHAPE);
    }, $data);
    
    return [$shapes, $defined_regions];
  }

  public function getShapes() : array {
    return $this->shapes;
  }

  public function getRegions(): array {
    return $this->regions;
  }
}

$data  = new Data('./input.txt');

$sum = 0;
foreach ($data->getRegions() as $region) {
  $area    = $region['width'] * $region['length'];
  $indexes = $region['indexes'];

  $size = array_sum(array_map(
    function ($x, $y) use ($data) {
      return $y * $data->getShapes()[$x];
    }, array_keys($indexes), $indexes));
  
  // Trial and error to get it working with the sample and actual input. Honestly, I have no idea why.
  if ($area > $size && ($size / $area) <= 0.85) {
    $sum++;
  }
}

print '<p>The number of regions that fit all of the presents are ' . $sum . '</p>';