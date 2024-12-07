<?php

class Calibration {
  const OPERATION_ADD      = '+';
  const OPERATION_MULTIPLY = '*';
  const OPERATION_CONCAT   = '|'; // The double pipe (||) messes with my split

  private $data;

  public function __construct($data = []) {
    $this->data = $data;
  }

  public function findTotals($operations) {    
    return $this->calculate($this->data, $operations);
  }
  
  public function calculate($data, array $operations) {
    $sum = 0;

    foreach ($data as $key => $line) {
        $result      = $line['result'];
        $test_values = $line['test_values'];

        if (count($test_values) == 1) { continue; }
        
        $remaining    = count($test_values) - 1;
        $combinations = $this->combine($remaining, $operations);

        foreach ($combinations as $combination) {
          $first = $test_values[0];

          foreach (str_split($combination) as $index => $operator) {
            switch($operator) {
              case self::OPERATION_ADD:
                $first += $test_values[$index + 1];
                break;
              
              case self::OPERATION_MULTIPLY:
                $first *= $test_values[$index + 1];
                break;

              case self::OPERATION_CONCAT:
                $first .= $test_values[$index + 1];
                break;
            }
          }
          
          if ($result == $first) {
            $sum += $result;
            break;
          }
        }
    }
    return $sum;
  }

  public function combine($remaining, $operations) {
    
    if (!$remaining) {
      return [''];
    }

    $result = [];
    foreach ($operations as $operator) {
      foreach ($this->combine($remaining - 1, $operations) as $combination) {
        $result[] = $operator . $combination;
      }
    }
    
    return $result;
  }
}

// ----------------------------------------------------------------
// MAIN
$data   = [];
$handle = fopen("input.txt", "r");
$i      = 0;

if ($handle) {
  while (($line = fgets($handle)) !== false) {
    $parts    = explode(': ', $line);
    $data[$i]['result']      = $parts[0];
    $data[$i]['test_values'] = explode(' ', $parts[1]);
    $i++;
  }  
  fclose($handle);
}

$calibration = new Calibration($data);
print '<p>Q1: The first answer is ' . $calibration->findTotals([Calibration::OPERATION_MULTIPLY, Calibration::OPERATION_ADD]) . '</p>';
print '<p>Q2: The second answer is ' . $calibration->findTotals([Calibration::OPERATION_MULTIPLY, Calibration::OPERATION_ADD, Calibration::OPERATION_CONCAT]) . '</p>';