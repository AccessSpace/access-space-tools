<?php
  
  $iNumberOfSides = 6;
  //$iRadius = 250;
  $iOutputWidth = 500;
  $iOutputHeight = 500;
  $sHREFDefault = 'Fagus_sylvatica_autumn_leaves.jpg';
  $sHREF = $sHREFDefault;
  $iRotationDuration = 0;
  $iSlipDuration = 0;
  $iOrgScale = 2;
  $iIncludeJS = 0;
  
  if ($_REQUEST['sides']) $iNumberOfSides = $_REQUEST['sides'];
  //if ($_REQUEST['radius']) $iRadius = $_REQUEST['radius'];
  if ($_REQUEST['width']) $iOutputWidth = $_REQUEST['width'];
  if ($_REQUEST['height']) $iOutputHeight = $_REQUEST['height'];
  if ($_REQUEST['href']) $sHREF = $_REQUEST['href'];
  if (isset($_REQUEST['slipduration'])) $iSlipDuration = $_REQUEST['slipduration'];
  if (isset($_REQUEST['rotationduration'])) $iRotationDuration = $_REQUEST['rotationduration'];
  if ($_REQUEST['scale']) $iOrgScale = $_REQUEST['scale'];
  if ($_REQUEST['includejs']) $iIncludeJS = $_REQUEST['includejs'];
  
  $iRadius = round(sqrt(($iOutputWidth * $iOutputWidth) + ($iOutputHeight * $iOutputHeight)) + 2, 1);
  
  $phi = M_PI/$iNumberOfSides;
  $fDeg = rad2deg($phi);
  $fX = round($iRadius * cos($phi), 1);
  $fY = round($iRadius * sin($phi), 1);
  $sTriangleCoord = "1,1 ".($fX+1).','.($fY+1).','.($fX+1).',1';
  $sClipCoord = "0,0 $fX,$fY, $fX,0";
  $sTriangleCoord = $sClipCoord; 
  $aImageData = getimagesize(str_replace(' ', '%20', $sHREF));
  if($aImageData == false)
  {
    error('No Image data');
  }
  $iImageWidth = $iOutputWidth * $iOrgScale;
  $iImageHeight = $aImageData[1] * ($iImageWidth / $aImageData[0]);
  
  header('Content-Type: image/svg+xml');

  echo '<?xml version="1.0" encoding="ISO-8859-1" standalone="no"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 20010904//EN"
    "http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd">';
?>

<svg width="<?php echo $iOutputWidth; ?>" height="<?php echo $iOutputHeight; ?>"
     xmlns="http://www.w3.org/2000/svg"
     xmlns:xlink="http://www.w3.org/1999/xlink">
     <?php
      if ($iIncludeJS)
      {
        $sScript = file_get_contents('smil.user.js');
        echo '<script type="text/ecmascript">'."\n<![CDATA[\n".$sScript."\n// ]]>\n</script>";
      }
      else
      {
        echo '<script type="text/ecmascript" xlink:href="smil.user.js"/>';
      }
     ?>
    <defs>
        <clipPath id="cp" clipPathUnits="userSpaceOnUse">
            <polygon points="<?=$sClipCoord; ?>"/>
        </clipPath>

        <g id="r1">
            <g>
                <g>
                    <image x="0" y="0" width="<?=$iImageWidth; ?>" height="<?=$iImageHeight; ?>" xlink:href="<?=$sHREF; ?>" />
                </g>
                <?php 
                if($iSlipDuration !== 0 ){?>
                  <animateTransform attributeName="transform" type="translate" begin="0s" dur="<?=$iSlipDuration; ?>s" values="0,0;<?=(($fX - 2) - $iImageWidth); ?>,<?=(($fY - 2) - $iImageHeight); ?>;0,0" repeatCount="indefinite"/>
                <?php }?>
                
            </g>
            
        </g>

        <polygon id="pg1" points="<?=$sTriangleCoord; ?>" style="stroke: none; fill: none"/>
    </defs>
    <g transform="translate(<?=$iOutputWidth / 2; ?>,<?=($iOutputHeight / 2); ?>)">
      <g>
        <g>
          <?php 
          for($i = 0 ; $i < $iNumberOfSides ; $i ++)
          {
            ?>
              <g transform="rotate(<?=($fDeg * $i *2); ?>)" style="clip-path: url(#cp)">
                  <use xlink:href="#pg1"/>
                  <use xlink:href="#r1"/>
              </g>
            <?php
          }
          ?>
        </g>
        <g transform="scale(1,-1)">
          <?php 
          for($i = 0 ; $i < $iNumberOfSides ; $i ++)
          {
            ?>
              <g transform="rotate(<?=($fDeg * $i *2); ?>)" style="clip-path: url(#cp)">
                  <use xlink:href="#pg1"/>
                  <use xlink:href="#r1"/>
              </g>
            <?php
          }
          ?>
        </g>
        
        <?php
          if($iRotationDuration !== 0 ){?>
            <animateTransform attributeName="transform" type="rotate" begin="0s" dur="<?=$iRotationDuration; ?>s" values="0;359" repeatCount="indefinite"/>
        <?php }?>
                
        
      </g>
      
    </g>
</svg>
  
  
