<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Advent of Code 2025</title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

  <style type="text/css">
  div.card {
    margin-top:20px;
  }
  </style>
</head>

<body class="d-flex flex-column h-100">

  <main class="flex-shrink-0">
    <div class="container">
      <h1 class="mt-5">Advent of Code 2025</h1>

      <div class="alert alert-warning" role="alert">
        <h3 class="alert-heading">Pages may take a while to load</h3>
        <p>Because of the complexity of the processes, some pages may take a while to load. Please be patient - hopefully the pages do not time out :p</p>
      </div>

      <div class="d-grid gap-3">

      <?php
      $folders = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25'];

      foreach ($folders as $folder) {
        $file = $folder . '/index.php';
        if (file_exists($file)) {
          print '<div class="card p-2 bg-light border">';
          print "\n\t" . '<div class="card-body">';
          print "\n\t\t" . '<h2 class="card-title">Day ' . $folder . '</h2>';
          print "\n\t\t" . '<p class="card-text">The solutions for Day ' . $folder . ' of the challenge</p>';
          print "\n\t\t" . '<a href="' . $folder . '/" class="btn btn-primary" target="_blank">' . $folder . ' - December results</a>';
          print "\n\t" . '</div>';
          print "\n" . '</div>';
        }
      }
      ?>    
      
    </div>
  </main>

  <footer class="footer py-3 bg-light mt-4">
    <div class="container text-center">
      <span class="text-muted">Learning Team</span>
    </div>
  </footer>

   
</body>
</html>