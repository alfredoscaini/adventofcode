<?php

class Device {
  private $name;
  private $outputs = [];
  
  public function __construct($name = '', $outputs = []) {
    $this->name   = $name;
    $this->outputs = $outputs;
  }  

  public function getOutputs() {
    return $this->outputs;
  } 

  public function getName() {
    return $this->name;
  } 
}

$data = file_get_contents('./input.txt');
$data = explode("\n", $data);

$devices = [];
for ($i = 0; $i < count($data); $i++) {
  list($device, $output) = explode(": ", $data[$i]);
  $devices[$device] = new Device($device, explode(" ", $output));
}

function countPaths($devices, $start, $end) : int {
  static $keys = [];
  $count = 0;

  if ($start != $end) {
    $key = $start . '-' . $end;

    if (array_key_exists($key, $keys)) {
      return $keys[$key];
    }

    if (array_key_exists($start, $devices)) {
      foreach ($devices[$start]->getOutputs() as $output) {
        $count += countPaths($devices, $output, $end);
      }
    }
    return $keys[$key] = $count;
  }

  return 1;
}

$start = 'you';
$end   = 'out';

$paths = countPaths($devices, $start, $end);
print '<p>The total number of paths are ' . $paths . '</p>';

$paths = 0;

$start = ['svr'];
$end   = ['dac', 'fft'];
foreach ($end as $output) {
 $paths += countPaths($devices, $start[0], $output);
}

$start = ['svr', 'dac', 'fft'];
$end   = ['dac', 'fft', 'out'];

$paths = countPaths($devices, $start[0], $end[0]) * countPaths($devices, $start[1], $end[1]) * countPaths($devices, $start[2], $end[2]) + 
         countPaths($devices, $start[0], $end[1]) * countPaths($devices, $start[2], $end[0]) * countPaths($devices, $start[1], $end[2]);

print '<p>The total number of paths with dac and fft in them are ' . $paths . '</p>';