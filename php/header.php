<?php defined('BASE') || exit('Restricted!'); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?php echo $title; ?></title>
<base href="<?php echo BASE; ?>" />
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<script src="vendor/jquery/jquery-3.4.1.min.js" type="text/javascript"></script>
<link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen" />
<script src="vendor/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<link href="vendor/font-awesome/css/all.min.css" rel="stylesheet" type="text/css" />
<link href="vendor/3235/stylesheet.css" rel="stylesheet">
</head>
<body>
<header>
  <nav class="navbar navbar-expand-md fixed-top">
    <a class="navbar-brand" href="#"><img src="https://private-parts.web.app/static/media/logo512.36ef65f1.png" style="height:30px;" /> Private Parts</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item active">
          <a class="nav-link" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="about.php">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="education.php">Education</a>
        </li>
      </ul>
    </div>
  </nav>
</header>