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

$paths = 1;
$start = ['svr', 'fft', 'dac'];
$end   = ['fft', 'dac', 'out'];

for ($i = 0; $i < count($start); $i++) {
  $paths *= countPaths($devices, $start[$i], $end[$i]);
}

print '<p>The total number of paths with dac and fft in them are ' . $paths . '</p>';