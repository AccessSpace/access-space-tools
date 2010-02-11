<?php

/**
 * An animated actor in the full CMA scene
 * 
 * This would be a kaleioscope or clippath ainmation or simliar.
 * 
 * This class will provide all the utility methods for getting a control panel and the svg both via php and javascript
 * 
 * The member variables of this class are a bit in the air as all subclassess are allowed as many as they like we will try to secure this up later.
 * 
 * @author martyn
 */
class actor
{
	/**
	 * @var string an identifier for this instance to put in xml ids etc.
	 */
	var $sInstance = '';
	
	/**
	 * @var string the path of the directory the module is defined in
	 */
	var $sModulePath = '';
	
	
	/**
		* 
		* @var array
		* array of controls
		*/
		var $aControls = array();

	/**
		* 
		* @var array
		* array of values
		*/
		var $aValues = array();
	
	
    /**
     * @param array $aParams
     * @param bool $bCalc  
     * @return bool
     */
    function __construct($aParams, $bCalc = true)
    {
       if(isset($aParams['sInstance']))
       { 
         $this->sInstance = $aParams['sInstance'];
       }
       else
       {
       
         $this->sInstance = time()%1000; // just a test string
       }
       
       foreach($aParams as $sKey => $xValue)
       {
         $this->aValues[$sKey] = $xValue;
       }
       
       $this->sModulePath = CONST_CMA_MODULES.(str_replace("actor_","", get_class($this)));
       if($bCalc)
       {
       	$this->calc();
       }
       return true;
    }
    
    /**
     * sets any additional member variable of this object needed for the template
     */
    function calc()
    {
          
    }
    
    /**
     * @param string $sTemplateName
     * @return string
     */
    function expandTemplate($sTemplateName)
    {
      $aValues = $this->aValues;
      $aValues['sInstance'] = $this->sInstance;
      $sJSON = json_encode($aValues);
      $sTemplateFile = $this->sModulePath.'/'.$sTemplateName;
      $sTemplate = file_get_contents($sTemplateFile);
      $oTemplate = new JsonTemplate($sTemplate);
      $sOut = $oTemplate->expand($sJSON);
      return $sOut;	
    }
    
    function getDefs()
    {
      return $this->expandTemplate('defs.svg');
    }
    
    
    function getBody()
    {
      return $this->expandTemplate('body.svg');
    }
    
    function getAnimatedXY($aControlPath, $fMaxX = 100, $fMaxY = 100)
    {
      $aClosedPath = $aControlPath;
      $aClosedPath[] = $aControlPath[0];
      $aAnim = array();
      foreach($aClosedPath as $aPos)
      {
        $aAnim[] = ($aPos['x'] / 100 * $fMaxX) .",".($aPos['y'] / 100 *$fMaxY);
      }
     
      $sAnimation = join(';', $aAnim);
      return $sAnimation;
    }
  
	
}