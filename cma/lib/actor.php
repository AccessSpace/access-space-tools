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
	var $sInstance;
	
	/**
	 * @var string the path of the directory the module is defined in
	 */
	var $sModulePath;
	
    /**
     * @param array $aParams
     * @param bool $bCalc  
     * @return bool
     */
    function __construct($aParams, $bCalc = true)
    {
       $this->sInstance = time()%1000; // just a test string
       foreach($aParams as $sKey => $xValue)
       {
         $this->$sKey = $xValue;
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
	  $sJSON = json_encode($this);
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
  
	
}