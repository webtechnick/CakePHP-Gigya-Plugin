<?php
/**
  * REST API to Gigya service.
  *
  * Create a datasource in your config/database.php
  * 
  * var $gigya = array(
  *   'datasource' => 'Gigya.GigyaSource',
  * );
  *
  * $Gigya = ConnectionManager::getDataSource('gigya');
	* $result = $Gigya->getUserInfo();
  *
  * @author Nick Baker
  * @version 0.1
  * @license MIT
  */
App::import('Core', 'Xml');
App::import('Core', 'HttpSocket');
App::import('Lib', 'Gigya.GigyaUtil');
class GigyaSource extends DataSource{
  /**
    * Description of gigya
    * @access public
    * @var string
    */
  var $description = "Gigya datasource";
  
  /**
    * the host to call from gigya
    * @access public
    * @var string
    */
  var $host = "socialize-api.gigya.com";
  
  /**
    * The scheme to make the call to gigya by
    * @access public
    * @var string
    */
  var $scheme = 'https';
  
  /**
    * The method name to call on the API
    * @access public
    * @var string
    */
  var $path = null;
  
  /**
    * HttpSocket object
    * @access public
    * @var HttpSocket
    */
  var $Http = null;
  
  /**
    * Query array
    * @access public
    * @var array
    */
  var $query = null;
  
  /**
    * params array complete with query and signature and apikey to pass to Gigya
    * @access protected
    * @var array
    */
  var $_params = null;
  
  /**
    * Requests Logs
    * @access private
    * @var array
    */
  var $__requestLog = array();
  
  /**
    * All available available functions
    * @access public
    * @var array
    */
  var $availableMethods = array(
    'disconnect',
    'getAlbums',
    'getFriendsInfo',
    'getPhotos',
    'getRawData',
    'getSessionInfo',
    'getUserInfo',
    'linkAccounts',
    'publishUserAction',
    'sendNotifications',
    'setStatus',
    'unlinkAccounts',
  );
  
  /**
    * Track errors
    * @access public
    * @var array
    */
  var $errors = array();
  
  /**
    * Append HttpSocket to Http and load the apiKey and secret configurations
    * 
    * @param array of config options
    */ 
  function __construct($config) {
    $config = array_merge(
      array(
        'apiKey' => GigyaUtil::getApiKey(),
        'secret' => GigyaUtil::getSecret(),
      ),
      $config
    );
    
    parent::__construct($config);
    $this->Http = new HttpSocket();
    
    if(!isset($this->config['apiKey'])){
      $this->_error('apiKey is not detected.');
    }
    if(!isset($this->config['secret'])){
      $this->_error('secret is not detected.');
    }
  }
  
  /**
    * The magic method to make all REST API calls to Gigya
    * 
    * Example:
    * - $Gigya->getUserInfo(array());
    * - $Gigya->getAlbums();
    * - $Gigya->linkAccounts();
    * - $Gigya->setStatus();
    * - $Gigya->publishUserAction();
    * - etc..
    *
    * @see availableMethods for a full list of available methods for you to use
    * @link http://wiki.gigya.com/030_API_reference/REST_API
    * @param array of options to for the method name
    * @access public
    * @return mixed array of result or false if method not withon availalb methods. 
    */
  function __call($method, $params){
    if(in_array($method, $this->availableMethods)){
      $this->path = "/socialize.$method";
      $this->query = array_shift($params);
      return $this->__makeRequest();
    }
    else {
      $this->_error("$method is not a valid method");
      return false;
    }
  }
  
  /**
    * trigger an error and set errors for user to review
    */
    function _error($msg){
      $error = __($msg, true);
      //trigger_error($error, E_USER_WARNING);
      $this->errors[] = $error;
    }
  
  /**
    * Actually preform the request to Gigya
    *
    * @return mixed array of the resulting request or false if unable to contact server
    * @access private
    */
  function __makeRequest(){
    $this->_params = $this->__buildParams();
    $url = array('scheme' => $this->scheme, 'host' => $this->host, 'path' => $this->path);
    $this->__requestLog[] = array('url' => $url, 'params' => $this->_params);
    $retval = $this->Http->get($url, $this->_params);
    $retval = Set::reverse(new Xml($retval));
    return $retval;
  }
  
  /**
    * Play nice with the DebugKit
    * 
    * @param boolean sorted ignored
    * @param boolean clear will clear the log if set to true (default)
    */
  function getLog($sorted = false, $clear = true){
    $log = $this->__paramsLog;
    if($clear){
      $this->__paramsLog = array();
    }
    return array('log' => $log, 'count' => count($log), 'time' => 'Unknown');
  }
  
  /**
    * Build the params to send to gigya
    * 
    * @return array of query
    */
  function __buildParams(){
    return array_merge(
      $this->query,
      array(
        'apiKey' => $this->config['apiKey'],
        'secret' => $this->config['secret'],
      )
    );
  }
}
?>