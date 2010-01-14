<?php

  class actor_kaleidoscope extends actor
  {
    
    var $iNumberOfSides = 3;
    var $iOutputWidth = 500;
    var $iOutputHeight = 500;
    var $sHREF = 'images/Fagus_sylvatica_autumn_leaves.jpg';
    var $iRotationDuration = 60;
    var $iSlipDuration = 60;
    var $iOrgScale = 2;
    var $iIncludeJS = 0;
    
       
    function calc()
    {
          
      $this->iMidWidth = $this->iOutputWidth / 2; 
      $this->iMidHeight = $this->iOutputHeight / 2;
      
      //echo "\n<br><pre>\noKaleidoscope =" .var_export($this, TRUE)."</pre>";
      $this->iRadius = round(sqrt(($this->iOutputWidth * $this->iOutputWidth) + ($this->iOutputHeight * $this->iOutputHeight)) + 2, 1);
      $this->phi = M_PI/$this->iNumberOfSides;
      $this->fDeg = rad2deg($this->phi);
      $this->fX = round($this->iRadius * cos($this->phi), 1);
      $this->fY = round($this->iRadius * sin($this->phi), 1);
      $this->sTriangleCoord = "1,1 ".($this->fX+1).','.($this->fY+1).','.($this->fX+1).',1';
      $this->sClipCoord = "0,0 $this->fX,$this->fY, $this->fX,0";
      $this->sTriangleCoord = $this->sClipCoord; 
      $this->aImageData = getimagesize(str_replace(' ', '%20', $this->sHREF));
      if($this->aImageData == false)
      {
        error('No Image data');
      }
      $this->iImageWidth = $this->iOutputWidth * $this->iOrgScale;
      $this->iImageHeight = $this->aImageData[1] * ($this->iImageWidth / $this->aImageData[0]);
      
      $this->aRotations = array();
      for($i = 0 ; $i < $this->iNumberOfSides ; $i ++)
      {
        $this->aRotations[] = array('fAngle' => $this->fDeg * $i *2);
      }
      //echo "\n<br><pre>\noKaleidoscope =" .var_export($this, TRUE)."</pre>";
      
      $this->aSlip['start']= array('x'=> 0, 'y'=>0);
      
      $this->aSlip['end']= array('x'=> ($this->fX - 2) - $this->iImageWidth,
                        'y'=>($this->fY - 2) - $this->iImageHeight);
    }
    
  }
