<?php

  class actor_sierpinsky extends actor
  {
    
	/**
		* 
		* @var array
		* array of controls
		*/
		var $aControls = array(
		  );

	/**
		* 
		* @var array
		* array of values
		*/
		var $aValues = array(
      'levels' => 6,
      'size' => 200,
      'pos_duration' => 30,
      'pos_control' => array(
                          array('x'=>150,'y'=>150),
                          array('x'=>200,'y'=>200),
                        ),
      
      'rotation_duration' => 30
    );

	
	/**
		* 
		* @var array
		* array of calculated values
		*/
		var $aCalculated = array();
		
		
   
    
       
    function calc()
    {
      $aLevels = array();
      for($i = 1; $i <= $this->aValues['levels']; $i++)
      {
        $aLevels[] = array('level' => $i, 'last' => $i -1);
      }
      $this->aValues['aLevels'] = $aLevels;
      $this->aValues['scale'] = $this->aValues['size'] / 2;
      $this->aValues['pos_path'] = $this->getAnimatedXY($this->aValues['pos_control']);
      //echo "\n<br><pre>\nthis->aValues =" .var_export($this->aValues, TRUE)."</pre>";
      
    }
    
    
  }
