<?php

  $iCycleStarted = $_REQUEST['s'];
  if(!$iCycleStarted)
  {
    $iCycleStarted = time();
  }
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    
    <script type="text/javascript">
      var iCycleStarted = <?php echo $iCycleStarted ?>;
    </script>
    <script type="text/javascript" src="js/mootools.js"></script>
    <script type="text/javascript" src="js/mooinfo.js"></script>
  </body>
    <style type="text/css">
  /* Just some nice colors */
  /* Vertical centering: make div as large as viewport and use table layout */
  body{ cursor:url('Invisible.cur'), crosshair; background-color:#000000; color:#FFFFFF}
  h1{font-size:50px;}
  div.container {top: 0; left: 0; width: 100%; height: 100%;
    position: fixed; display: table;}
  p {display: table-cell; vertical-align: middle;}

  /* Horizontal centering of image: set left & right margins to 'auto' */
  img.displayed {display block; margin: auto auto; width:100%; cursor:url('Invisible.cur'), crosshair;}

  /* Also center the lines in the paragraph */
  p {text-align: center;}
</style>
  </head>
  <body>
  <div class="container">
  <p id="content">
  </p>
  </div>

</html>