<?php 

$data   = [];
$handle = fopen("./input.txt", "r");
if ($handle) {
  while (($line = fgets($handle)) !== false) {
    $data[] = explode('   ', $line);  
  }  
  fclose($handle);
}

$list_left  = [];
$list_right = [];

foreach ($data as $list) {
  $list_left[]  = $list[0];
  $list_right[] = $list[1];
}


// Q1: Sort the two lists and compare how far apart the value on the left is to the value on the right
// watch out for negative numbers and get the absolute separation.
sort($list_left);
sort($list_right);

$total_difference = 0; // calculate the difference between each set of compared values.
for ( $i = 0; $i < count($list_left); $i++) {
  $left  = (count($list_left) > $i)  ? $list_left[$i]  : -1;
  $right = (count($list_right) > $i) ? $list_right[$i] : -1;

  if ($left !== -1 && $right !== -1) {
    $total_difference += abs($right - $left);
  }
}

print '<p>Q1: The sum of all distances is ' . $total_difference . '</p>'; // 2176849

// Q2: Find out how many times the left column appears in the right column and record the total number of 
// occurrences. Then multiply this value by the left column value to obtain the total similiarities.
$counts = [];
foreach ($list_left as $l_id) {
  $counts[$l_id] = 0;
    foreach ($list_right as $r_id) {
      if ($l_id == $r_id) {
        $counts[$l_id]++;
      }
    }
}

$total_similarity = 0;
foreach ($counts as $key => $value) {
  $total_similarity += ($key * $value);
}

print '<p>Q2: The sum of all similiarities is ' . $total_similarity . '</p>'; // 23384288