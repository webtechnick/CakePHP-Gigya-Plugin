<?php
/**
  * Gigya.Gigya helper generates javascript tags used for various social networks
  * This is used to build and show widgets, as well as build custom solutions
  *
  * @author Nick Baker
  * @version 0.1
  * @link http://www.webtechnick.com
  * @license MIT
  */
App::import('Lib', 'Gigya.GigyaUtil');
class GigyaHelper extends AppHelper{
  /**
    * Helpers to load with gigya helper
    * @access public
    * @var string
    */
  var $helpers = array('Html', 'Js');
  
  /**
    * Caches the apiKey
    * @access public
    * @var string
    */
  var $apiKey = null;
  
  /**
    * The javascript loader needed to do various calls
    * 
    * @access public
    * @var string
    */
  var $__loader = "http://cdn.gigya.com/JS/socialize.js?apikey=";
  
  /**
    * The base object to make all calls off of
    * @access public
    * @var string
    */
  var $__host = "gigya.services.socialize";
  
  /**
    * The default library to use
    *
    * @access public
    * @var string
    */
  var $library = 'Prototype';
  
  /**
    * The json object configuration to pass into each gigya call
    * 
    * @access public
    * @var string
    */
  var $conf = null;
  
  /**
    * Constructor sets the library to use and setsup the apiKey
    */
  function __construct($settings = array()){
    if(is_array($settings) && isset($settings[0])){
      $this->library = $settings[0];
    }
    elseif(is_string($settings)){
      $this->library = $settings;
    }
    
    $this->apiKey = GigyaUtil::getApiKey();
  }
  
  /**
    * Set the configuration params for every Gigya call.
    * @param array of options to parse into a json object
    * @return void
    */
  function setConf($options = array()){
    $this->conf = $this->Js->object($options);
  }
  
  /**
    * Setup the loader to be put into the head of the layout or anywhere you want to use gigya
    * @return scriptblock.
    */
  function loader(){
    $this->setConf(array('APIKey' => $this->apiKey));
    return $this->Html->script($this->__loader . $this->apiKey);
  }
  
  /**
    *
    * @link http://wiki.gigya.com/030_API_reference/010_Client_API/020_Methods/Socialize.showLoginUI
    * @param array of options
    * @return scriptBlock
    */
  function login($options = array()){
    if(isset($options['redirectURL'])){
      $options['redirectURL'] = Router::url($options['redirectURL']);
    }
    
    $options = array_merge(
      array(
        'redirectURL' => Router::url(array('plugin' => 'gigya', 'controller' => 'socialize', 'action' => 'login')),
        'enabledProviders' => 'facebook,myspace,twitter,linkedin,google',
      ),
      $options
    );
    return $this->showLoginUI($options);
  }
  
  /**
    * The magic method to make all javascript API calls to gigya
    * 
    * Example:
    * - $gigya->getUserInfo(array());
    * - $gigya->connect();
    * - $gigya->login();
    * - $gigya->logout();
    * - $gigya->notifyLogin();
    * - etc..
    *
    * @link http://wiki.gigya.com/030_API_reference/010_Client_API
    * @param array of options to for the method name
    *  - if option['buffer'] is set to true, returned will be the string instead of the full scriptBlock
    * @access public
    * @return scriptBlock of call or text of call to make 
    */
  function __call($method, $params){
    $options = array_shift($params);
    $json_params = is_array($options) ? $this->Js->object($options) : "{}";
    
    $script = "$this->__host.$method($this->conf, $json_params)";
    
    if(is_array($options) && isset($options['buffer']) && $options['buffer']){
      return $script;
    }
    return $this->Html->scriptBlock($script);
  }
}
?>