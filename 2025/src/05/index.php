<?php

class SystemList {
  private $ranges = [];
  private $ids    = [];
  
  public function __construct($file) {
    list($this->ranges, $this->ids) = $this->process($file);
  }

  private function process(string $file) : array {
    $data = file_get_contents($file);
    list($data1, $data2) = explode("\n\n", $data);

    $data1  = explode("\n", $data1);
    $ranges = [];
    for($i = 0; $i < count($data1); $i++) {
      $ranges[$i] = explode("-", $data1[$i]);
    }

    $ids = explode("\n", $data2);

    return [$ranges, $ids];
  }

  public function getRanges() {
    return $this->ranges;
  }

  public function getIDs() {
    return $this->ids;
  }

  public function findInRange($id, $range) : bool {
    return (
      ($id >= $range[0] && $id <= $range[1])
    ) ? true : false;
  }

  public function findInRange2($ranges = []) : int {
    $sum = 0;
    
    for ($i = 0; $i < count($ranges); $i++) {
      $start = &$ranges[$i][0];
      $end   = &$ranges[$i][1];

      for ($j = 1; $j < count($ranges); $j++) {
        $current_start = &$ranges[$j][0];
        $current_end   = &$ranges[$j][1];
        
        if ($i != $j) {
          if ($start >= $current_start && $start <= $current_end) {
            $current_end = max($end, $current_end);
            $start = 0;
            $end   = 0;
          } elseif ($end >= $current_start && $end <= $current_end) {
            $current_start = min($start, $current_start);
            $start = 0;
            $end   = 0;
          }
        }
      }
      
      if ($start && $end) {
        $sum += $end - $start + 1;
      }
    }

    return $sum;
  }
}

$sum  = [];
$list = new SystemList('./input.txt');

$ranges = $list->getRanges();
$ids    = $list->getIDs();

foreach ($ids as $id) {
  foreach ($ranges as $range) {
    if ($list->findInRange($id, $range)) {
      $sum[$id] = 1;
    }
  }
}

print '<p>Available ingredient count is ' . count($sum) . '</p>';
print '<p>All fresh ingredients count is ' . $list->findInRange2($ranges) . '</p>';

