<?php

  class actor_kaleidoscope extends actor
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
      'sides' => 3,
      'radius' => 150,
      //'output_width' => 300,
      //'output_height' => 300,
      
      'output_x' => 150,
      'output_y' => 150,
      'image_url' => 'images/Fagus_sylvatica_autumn_leaves.jpg',
      'rotation_duration' => 60,
      'slip_duration' => 60,
      'orginal_scale' => 2,
    );

	
	/**
		* 
		* @var array
		* array of calculated values
		*/
		var $aCalculated = array();
		
		
   
    
       
    function calc()
    {
          
      //$this->aValues['center_x'] = $this->aValues['output_width'] / 2; 
      //$this->aValues['center_y'] = $this->aValues['output_height'] / 2;
      
      //echo "\n<br><pre>\noKaleidoscope =" .var_export($this, TRUE)."</pre>";
      //$this->aValues['radius'] = round(sqrt(($this->aValues['output_width'] * $this->aValues['output_width']) + ($this->aValues['output_height'] * $this->aValues['output_height'])) + 2, 1);
      $this->aValues['angle_rad'] = M_PI/$this->aValues['sides'];
      $this->aValues['angle_deg'] = rad2deg($this->aValues['angle_rad']);
      $this->aValues['triangle_x'] = round($this->aValues['radius'] * cos($this->aValues['angle_rad']), 1);
      $this->aValues['triangle_y'] = round($this->aValues['radius'] * sin($this->aValues['angle_rad']), 1);
      $this->aValues['triangle_coord'] = "1,1 ".($this->aValues['triangle_x']+1).','.($this->aValues['triangle_y']+1).','.($this->aValues['triangle_x']+1).',1';
      $this->aValues['clip_coord'] = "0,0 ".$this->aValues['triangle_x'].",".$this->aValues['triangle_y'].", ".$this->aValues['triangle_x'].",0";
      $this->aValues['triangle_coord'] = $this->aValues['clip_coord']; 
      $this->aValues['image_data'] = getimagesize(str_replace(' ', '%20', $this->aValues['image_url']));
      
      if($this->aValues['image_data'] == false)
      {
        error('No Image data');
      }
      
      ///$this->aValues['image_width'] = $this->aValues['output_width'] * $this->aValues['orginal_scale'];
      $this->aValues['image_width'] = $this->aValues['radius'] * 2 * $this->aValues['orginal_scale'];
      $this->aValues['image_height'] = $this->aValues['image_data'][1] * ($this->aValues['image_width'] / $this->aValues['image_data'][0]);
      
      $this->aValues['rotations'] = array();
      
      for($i = 0 ; $i < $this->aValues['sides'] ; $i ++)
      {
        $this->aValues['rotations'][] = array('angle_deg' => $this->aValues['angle_deg'] * $i *2);
      }
      
      $this->aValues['slip']['start'] = array('x' => 0, 'y' => 0);
      
      $this->aValues['slip']['end'] = array('x'=> ($this->aValues['triangle_x'] - 2) - $this->aValues['image_width'],
                        'y'=>($this->aValues['triangle_y'] - 2) - $this->aValues['image_height']);
    }
  }
