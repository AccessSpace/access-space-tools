<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="controls.css" type="text/css" />
	<script type="text/javascript">
	var bControls = true;
	var bViewer = true;
	</script>
	<script type="text/javascript" src="mootools.js"></script>
	<script type="text/javascript" src="controls.js"></script>
	<title>Slider Demo</title>
</head>
<body><?php


  $iNumberOfSides = 3;
  $iOutputWidth = 500;
  $iOutputHeight = 500;
  $sHREFDefault = 'Fagus_sylvatica_autumn_leaves.jpg';
  $sHREF = $sHREFDefault;
  $iRotationDuration = 60;
  $iSlipDuration = 60;
  $iOrgScale = 2;
  $iIncludeJS = 0;
  
  if ($_REQUEST['sides']) $iNumberOfSides = $_REQUEST['sides'];
  if ($_REQUEST['width']) $iOutputWidth = $_REQUEST['width'];
  if ($_REQUEST['height']) $iOutputHeight = $_REQUEST['height'];
  if ($_REQUEST['href']) $sHREF = $_REQUEST['href'];
  if ($_REQUEST['slipduration']) $iSlipDuration = $_REQUEST['slipduration'];
  if ($_REQUEST['rotationduration']) $iRotationDuration = $_REQUEST['rotationduration'];
  if ($_REQUEST['scale']) $iOrgScale = $_REQUEST['scale'];
  if ($_REQUEST['includejs']) $iIncludeJS = $_REQUEST['includejs'];
  
  $sURL = "k.php?sides=$iNumberOfSides&width=$iOutputWidth&height=$iOutputHeight&slipduration=$iSlipDuration&rotationduration=$iRotationDuration&scale=$iOrgScale&includejs=$iIncludeJS&href=".urlencode($sHREF);
  
?>
<html>
  <body>
  <h1>Martyns friendly SVG Kaleidoscope builder</h1>
 <!-- <p>Try changeing the settings below or adding your own Image URL to create the effect you want.<br />
  This page will try to access your chosen image to get the file size of it but the SVG itself will call the image directly.<br />
  When you are happy right click on this <a target="_blank" href="<?php echo $sURL; ?>">link</a> and save where ever you like with an .svg extension<br />
  This svg will work in Opera as is, to work in Firefox you will need fakesmil there is an option below to include it in your svg or you can get  from <a href="smil.user.js">here</a> and pop in the same directory as you save svg.<br />
  Please check out my <a href="http://stretch.deedah.org/">main site</a> if youv'e not been before.-->
  </p> 
    <form id="controller">
      <table width="100%">
        <tr>
          <td>Sides</td>
          <td><input name="sides" size="4" min="3" max="20" class="slider" value="<?php echo $iNumberOfSides; ?>"/>
          </td>
          <td>Scale</td>
          <td><input name="scale" size="4" min="1" max="3" class="slider"value="<?php echo $iOrgScale; ?>"/></td>
        
        
          <td>Slip Duration</td>
          <td><input name="slipduration" size="4" min="0" max="120" class="slider" value="<?php echo $iSlipDuration; ?>"/></td>
          
        </tr>
        <tr>
          <td>Rotation Duration</td>
          <td><input name="rotationduration" size="4" min="0" max="120" class="slider" value="<?php echo $iRotationDuration; ?>"/></td>
        
        <!--  <td>Radius</td>
          <td><input name="radius" size="4" min="200" max="2000" class="slider" value="<?php echo $iRadius; ?>"/></td>
          -->
          <td>Width</td>
          <td><input name="width" size="4" min="200" max="1200" class="slider" value="<?php echo $iOutputWidth; ?>"/></td>
          <td>Height</td>
          <td><input name="height" size="4" min="200" max="1200" class="slider" value="<?php echo $iOutputHeight; ?>"/></td>
        </tr>
        <tr>
         <!-- <td>Include FakeSmil Javascript</td>
          <td><select name="includejs"><option value="0" <?php if ($iIncludeJS == 0 ) echo "selected"; ?>>No</option><option value="1" <?php if ($iIncludeJS == 1 ) echo "selected"; ?>>Yes</option></select></td>
         
          <td >Image URL</td>
          <td colspan="3"><input name="href" length="100" size="50" value="<?php echo $sHREF; ?>"/></td>-->
          <td >Image URL</td>
          <td colspan="5"><input name="href" length="100" size="50" value="<?php echo $sHREF; ?>"/></td>
          <!--<td>DisplaTypes</td>
          <td><select name="height" size="4" min="200" max="2000" class="slider" value="<?php echo $iOutputHeight; ?>"/></td>-->
          
          
        </tr>
        <tr>
          <td colspan="6">
          <input type="submit" value="Submit" width="100%"/></td>
          
        </tr>
      </table>
    </form>
    <!--
    <?php if($sHREF == $sHREFDefault) {?>
      <p>Photographer Luis Fernández García L. Fdez. This image is licensed under the <a href="http://en.wikipedia.org/wiki/Creative_Commons" title="Creative Commons">Creative Commons</a> "<a href="http://creativecommons.org/licenses/by-sa/2.1/es/deed.en"  title="http://creativecommons.org/licenses/by-sa/2.1/es/deed.en" rel="nofollow">Attribution-ShareAlike&nbsp;2.1&nbsp;Spain</a>" Licence</p>
    <?php } ?>-->
    <!--<p>URL :</a></p>-->
    
    
    <iframe id="outputframe" src="<?php echo $sURL; ?>" width="<?php echo $iOutputWidth; ?>" height="<?php echo $iOutputHeight; ?>" />
    
    
  </body>
</html>
