<?php
	$sSRC = "urbanmandala/dafodill hgrt.png"; //This will be the test one
//A_bike.gif  B_feet.gif	C_shadows_2.gif  D_feet_5.gif  E_feet_8.gif  F_feet_2.gif  G_feet_7.gif  H_feet_4.gif  I_feet_3.gif  J_feet_6.gif
	$aMapping = array(
		'192.168.1.74' => 'anagram/A_bike.gif',
		'192.168.1.77' => 'anagram/B_feet.gif',
		'192.168.1.78' => 'anagram/C_shadows_2.gif',
		'192.168.1.79' => 'anagram/D_feet_5.gif',
		'192.168.1.82' => 'anagram/E_feet_8.gif',
		'192.168.1.81' => 'anagram/F_feet_2.gif',
		'192.168.1.76' => 'anagram/G_feet_7.gif',
		'192.168.1.75' => 'anagram/H_feet_4.gif',
		'192.168.1.73' => 'anagram/I_feet_3.gif',
		'192.168.1.72' => 'anagram/J_feet_6.gif',
		);

	$sIP = $_SERVER['REMOTE_ADDR'];
	
	if(isset($aMapping[$sIP]))
	{
		$sSRC = $aMapping[$sIP];
	}
	
?><html>
  <head>
  <style type="text/css">
  /* Just some nice colors */
  /* Vertical centering: make div as large as viewport and use table layout */
  body{ cursor:url('Invisible.cur'), crosshair; background-color:#000000;}
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
<div class=container>
  <p>
   <img class="displayed" src="<?php echo $sSRC; ?>" />
  </p>
</div>
	</body>
</html>
