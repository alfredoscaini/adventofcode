<?php 
class Report {
  private $data    = [];  
  public  $is_safe = false;
  private $dataset;

  const MIN = 1;
  const MAX = 3;

  const LEVEL_UP   = 'up';
  const LEVEL_DOWN = 'down';
  const LEVEL_SAME = 'same';


  public function __construct($data = []) {
    $this->data = $data;
    $this->assignParameters($this->data);
  }

  private function assignParameters($data = []) {
    $dataset = [
      'level'  => [],
      'change' => []
    ];

    for ($i = 0; $i < count($data); $i++) {
      $current = $data[$i];
      $next    = (($i + 1) < count($data)) ? $data[$i+1] : false;

      if ($next) {        
        $dataset['level'][]  = ($current < $next) ? self::LEVEL_UP : (($current > $next) ? self::LEVEL_DOWN : self::LEVEL_SAME);
        $dataset['change'][] = abs($current - $next);
      }     
    }   

    $this->dataset = $dataset;
    $this->is_safe = $this->validateReport($dataset);
  }

  private function validateReport($data = []) {
    $is_safe     = false;
    $dataset     = $data;

    $within_parameters = true;
    foreach ($dataset['change'] as $change) {
      if ($change < self::MIN || $change > self::MAX) {
        $within_parameters = false;
      }
    }

    $level_count = count(array_count_values($dataset['level'])); // if there are problems this value will be greater than one

    if ($within_parameters && $level_count == 1) {
      $is_safe = true;
    }

    return $is_safe;
  }

  public function problemDampenerFixesReport() : bool {
    $data = $this->data;

    for ($i = 0; $i < count($data); $i++) {
      $check_data = $data;      
      array_splice($check_data, $i, 1);
      $this->assignParameters($check_data);
      if ($this->is_safe) {
        return true;
      }
    }   

    return false;
  }
}

// ----------------------------------------------------------------
// MAIN
$data   = [];
$handle = fopen("input.txt", "r");
if ($handle) {
  while (($line = fgets($handle)) !== false) {
    $data[] = explode(' ', $line);  
  }  
  fclose($handle);
}


$safe_reports = [];
$bad_reports  = [];
foreach ($data as $levels) {
  $report = new Report($levels);

  if ($report->is_safe) {
    $safe_reports[] = $report;
  } else {
    $bad_reports[] = $report;
  }
}

print '<p>Q1: The number of safe reports is : ' . count($safe_reports); // 472

foreach ($bad_reports as $report) {
  if ($report->problemDampenerFixesReport()) {
    $safe_reports[] = $report;
  }
}

print '<p>Q2: The number of safe reports with the Problem Dampener applied is : ' . count($safe_reports);

?>