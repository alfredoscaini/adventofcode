<?php 


// ----------------------------------------------------------------
// MAIN
$data   = [];
$handle = fopen("input.txt", "r");
if ($handle) {
  while (($line = fgets($handle)) !== false) {
    $data[] = $line;
  }  
  fclose($handle);
}

// --------------------------------------------------------------------
// Question 1
// --------------------------------------------------------------------
$found = [];
$pattern = "/mul\((\d+),(\d+)\)/";
foreach ($data as $line) {
  preg_match_all($pattern, $line, $matches, PREG_PATTERN_ORDER);
  $found = array_merge($found, $matches[0]);
}

$sum = 0;
foreach ($found as $product) {
  $numbers   = explode(',', str_replace(['mul', '(', ')'], ['', '', ''], $product));
  $sum += ($numbers[0] * $numbers[1]);
}

print 'Q1: The total sum is ' . $sum . '</p>';

// --------------------------------------------------------------------
// Question 2
// --------------------------------------------------------------------
$found = [];
$pattern = "/(do|don't)\(\)|mul\((\d+),(\d+)\)/";
foreach ($data as $line) {
  preg_match_all($pattern, $line, $matches, PREG_PATTERN_ORDER);
  $found = array_merge($found, $matches[0]);
}

$sum = 0;
$switch = 'do()';
foreach ($found as $product) {   
    if (in_array($product, ['do()', "don't()"])) {
      $switch = $product;
      continue;
    }

    if ($switch == 'do()') {
      $numbers   = explode(',', str_replace(['mul', '(', ')'], ['', '', ''], $product));
      $sum += ($numbers[0] * $numbers[1]);
   }
}

print 'Q2: The total sum is ' . $sum . '</p>';