<?php 

class Computer {
  private $computer;
  private $networks = [];

  public function __construct($computer) {
    $this->computer = $computer;
  }

  public function addNetwork($computer) {
    if (!in_array($computer, $this->networks)) {
      $this->networks[] = $computer;
    }
  }

  public function getNetworks() : array {
    return $this->networks;
  }
}


// ---------------------------------------------------------
// Main
function getLANs($computers, $chiefs) {
  $lans = [];

  foreach ($computers as $computerA => $computer) {
    $networkA = $computer->getNetworks();
    foreach ($networkA as $computerB) {
      $networkB = $computers[$computerB]->getNetworks();
      foreach ($networkB as $computerC) {
        $networkC = $computers[$computerC]->getNetworks();
  
        $set = [$computerA, $computerB, $computerC];
        sort($set, SORT_STRING);
        
        if (in_array($computerA, $networkC) && count(array_intersect($set, $chiefs)) && !in_array($set, $lans)) {
          $lans[] = $set;
          $sets[$computerA] = $set;
        }
      }
    }
  }

  return $lans;
}

function findSets($computers, $combination = []) {
  sort($combination);
  
  if (count($computers) == 0) {    
    return [implode(',', $combination)];
  }
  
  $result = [];
  foreach ($computers as $id => $computer) {
    $networks     = $computer->getNetworks();
    $combinations = array_merge($combination, [$id]);

    $lan = [];
    foreach ($networks as $networked_computer) {
      if (array_key_exists($networked_computer, $computers)) {
        $lan[$networked_computer] = $computers[$networked_computer];
      }        
    }

    $result = array_merge($result, findSets($lan, $combinations));
    unset($computers[$id]);
  }
  
  return $result;
}

$data      = explode("\n", file_get_contents('input.txt'));
$computers = [];
$chiefs    = [];

foreach ($data as $index => $network) {
  $starts_with_keyword = false;

  $set = explode('-', $network);

  foreach ($set as $computer) {
    if (!array_key_exists($computer, $computers)) {
      $computers[$computer] =  new Computer($computer);      
    }

    if (strpos($computer, 't') === 0 && !in_array($computer, $chiefs)) {
      $chiefs[] = $computer;
    }    
  }

  $computers[$set[0]]->addNetwork($set[1]);
  $computers[$set[1]]->addNetwork($set[0]);
}

$lans = getLANs($computers, $chiefs);
print '<p>Q1: The answer is ' . count($lans) . '</p>';

$sets = findSets($computers);
usort($sets, function($a, $b) { return strlen($b) <=> strlen($a); });

$password = array_shift($sets);
print '<p>Q2: The answer is ' . $password . '</p>';
