<?php
class DiskMap {
  const SPACE_AVAILABLE = '.';

  private $map;
  private $disk;
  private $compacted;

  public function __construct($datafile = '') {
    $this->map  = $this->processMap($datafile);
  }

  public function processDiskMap($use_whole_files = false) {
    $use_whole_files = ($use_whole_files) ? true : false;

    $this->disk      = $this->assignDisk($this->map, $use_whole_files);
    $this->compacted = $this->compact($this->disk, $use_whole_files);
  }

  public function checksum() {
    $sum  = 0;
    $data = $this->compacted;

    $counter = 0;
    foreach ($data as $block) {
      $item = (is_null($block)) ? 0 : $block;
      $sum += $counter++ * $item;
    }

    return $sum;
  }

  private function processMap($datafile = '') : array {
    if (file_exists($datafile)) {      
      return str_split(file_get_contents($datafile));
    }

    return [];
  }

  public function assignDisk($map = [], $use_whole_files = false) : array {
    $disk = [];
    $j    = 0;

    for ($i = 0; $i < count($map); $i++) {
      $disk = array_merge($disk, array_fill(0, intval($map[$i]), $j));
      
      if (($i + 1) < count($map)) {
        $disk = array_merge($disk, array_fill(0, intval($map[++$i]), null));
      }
      
      $j++;

    }

    return $disk;
  }

 private function compact($disk = [], $use_whole_files = false) : array { 
    $limit = count($disk) - 1;    

    if ($use_whole_files) {

      $data  = [];
      $j     = 0;

      for ($i = $limit; $i > 0; $i--) {
        if (is_numeric($disk[$i])) {
          if (!array_key_exists($disk[$i], $data)) {
            $data[$disk[$i]] = [
              'index' => [],
              'size'  => 0
            ];  
          }

          $data[$disk[$i]]['index'][] = $i;
          $data[$disk[$i]]['size']++;
        }
      }

      foreach ($data as $key => $info) {
        $indexes = $info['index'];
        $size    = $info['size'];
        $moved   = false;

        $last_index_to_search = $indexes[count($indexes) - 1];

        for ($disk_index = 0; $disk_index < $last_index_to_search; $disk_index++) {

          if (is_numeric($disk[$disk_index])) { continue; }

          // how many null elements are there?
          $length = 0;
          for ($null_index = $disk_index; $null_index < count($disk); $null_index++) {
            if (is_numeric($disk[$null_index])) {
              break;
            } 
            
            $length++;
          }

          if ($size <= $length) {            
            $blocks = array_fill(0, $size, $key);
            $moved  = true;
            for ($x = 0; $x < count($blocks); $x++) {    
              $disk[$disk_index] = $blocks[$x];                
              $disk_index++;
            }

            break;
          }
        }

        if ($moved) {          
          // set the original indexes where the previous block existed to null
          foreach ($indexes as $index) {
            $disk[$index] = null;
          }
        }
      }
    } else {

      for ($i = $limit; $i > 0; $i--) {
        $last_block = $disk[$i];
        
        for ($j = 0; $j < $i; $j++) {        
          if (is_null($disk[$j])) {
            $disk[$j] = $last_block;
            $disk[$i] = null;
            break;
          }
        }
      }
    }

    return $disk;
  }
}

// ----------------------------------------------------
// MAIN
$map = new DiskMap('input.txt');

$map->processDiskMap();
print '<p>Q1: The answer is ' . $map->checksum() . '</p>';

$map->processDiskMap(true);
print '<p>Q2: The answer is ' . $map->checksum() . '</p>';