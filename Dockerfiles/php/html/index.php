<?PHP
require_once 'dash.php';
date_default_timezone_set("Europe/Stockholm");

$currentApp= 0;
if(!isset($_SESSION)) session_start();
if(isSet($_SESSION['app'])) $currentApp= $_SESSION['app'];
else if(isSet($_COOKIE['app'])) $currentApp= $_COOKIE['app'];

$apps= array();
$dash= new Dash();
$apps= $dash->getApps();
if (count($apps)==0) {
  $apps[]= array('id'=>0, 'name'=>'');
  $currentApp= 0;
}

/*
if ($currentApp > (count($apps)-1)) {
  $currentApp= 0;
  $_SESSION['app']= 0;
  setcookie('app', 0, time()+15552000);
}
*/

$appsList= '';
$currentAppName= '';
foreach ($apps as $app) {
  $appsList.= '<li><a href="#" data-id='.$app['id'].'>'.$app['name'].'</a></li>';
  if ($currentApp == 0) $currentApp= $app['id'];
  if ($currentApp == $app['id']) $currentAppName= $app['name'];
}

$envNames= array();
$envNames= $dash->getEnvNames();

$envGroupNames= array();
$envGroupNames= $dash->getEnvGroupNames();

$deployments= array();
$deployments= $dash->getDeployments($currentApp);

$envGroups= array();
$envGroups= $dash->getEnvGroups($deployments);

$panels= '';
foreach ($envGroups as $envGroup) {
  $panels.= '
      <div class="panel panel-default envPanel">
        <div class="panel-heading">
          <h4 class="panel-title text-center">
            <a data-toggle="collapse" href="#collapseEnv'.$envGroup.'">'.$envGroupNames[$envGroup].'</a>
          </h4>
        </div>
        <div id="collapseEnv'.$envGroup.'" class="panel-collapse collapse in">
          <div class="panel-body">
            <div class="row">';
            foreach ($deployments as $deployment) {
              if ($deployment['envGroup']==$envGroup)
              $panels.= '
              <div class="col-sm-4 col-lg-3">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h4 class="panel-title text-center">'.$envNames[$deployment['env']].'</h4>
                  </div>
                  <div class="panel-body">Version:<span class="onRight"><b>'.$deployment['version'].'</b></span><br>Deployed On:<span class="onRight">'.$deployment['date'].'</span><br><a href="'.$deployment['log'].'">Deployment Log</a>
                  </div>
                </div>
              </div>';
            }
            $panels.= '
            </div>
          </div>
        </div>
      </div>
  ';
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="ARDA Dashboard">
    <title>ARDA Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="dash.css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  </head>
  <body>
    <div id='background-div' class='container-fluid'></div>
    <div class="mainContainer">
      <div id="topBar">
        <div class="dropdown">
          <button class="btn btn-default dropdown-toggle" type="button" id="appName" data-toggle="dropdown" data-id=<?php echo $currentApp?> aria-expanded="true">Application: <?php echo $currentAppName?> <span class="caret"></span></button>
          <ul class="dropdown-menu" aria-labelledby="appName">
            <?php echo $appsList ?>
          </ul>
        </div>
        <img src="logo.png">
      </div>
      <?php echo $panels ?>
    </div>
  </body>
</html>
<script>
  $(document).ready(function() {
    $(".dropdown li a").click(function(){
      var id= $(this).attr('data-id');
      $(this).parents('.dropdown').find('.dropdown-toggle').html('Application: '+$(this).text()+' <span class="caret"></span>');
      $(this).parents('.dropdown').find('.dropdown-toggle').attr('data-id', id);
      $.post("/selectApp.php", {app:id}, function() {
        location.reload();
      });
    });
  });
</script>