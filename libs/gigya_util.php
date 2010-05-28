<?php
/**
  * Helper class to preform some basic tasks. 
  *
  * @author Nick Baker
  * @version 0.1
  * @license MIT
  */
class GigyaUtil extends Object {
  
  /**
    * Gigya configurations stored in
    * app/config/gigya.php
    * @var array
    */
  public static $configs = array();
  
  /**
    * GigyaDataSource Object
    * @var GigyaDataSource Object
    */
  public static $GigyaSource = null;
  
  /**
    * Return version number
    * @return string version number
    * @access public
    */
  static function version(){
    return "1.0";
  }
  
  /**
    * Return description
    * @return string description
    * @access public
    */
  static function description(){
    return "CakePHP Gigya Plugin";
  }
  
  /**
    * Return author
    * @return string author
    * @access public
    */
  static function author(){
    return "Nick Baker";
  }
  
  /**
    * Testing getting a configuration option.
    * @param key to search for
    * @return mixed result of configuration key.
    * @access public
    */
  static function getConfig($key){
    if(isset(self::$configs[$key])){
      return self::$configs[$key];
    }
    //try configure setting
    if(self::$configs[$key] = Configure::read("Gigya.$key")){
      return self::$configs[$key];
    }
    //try load configuration file and try again.
    Configure::load('gigya');
    self::$configs = Configure::read('Gigya');
    if(self::$configs[$key] = Configure::read("Gigya.$key")){
      return self::$configs[$key];
    }
    
    return null;
  }

  /**
    * Get the ApiKey from the configuration file or from cache
    * @return string gigya api key
    * @access public
    */
  static function getApiKey(){
    return self::getConfig('apiKey');
  }
  
  /**
    * Get the secret from the configuration file or from cache
    * @return string gigya api key
    * @access public
    */
  static function getSecret(){
    return self::getConfig('secret');
  }
  
  /**
    * Generate a Gigya signature from a timestamp and a UID
    * 
    * @param string timestamp
    * @param string uid
    * @return string gigya signature
    */
  static function generateSignature($timestamp, $UID){
    $base_string = $timestamp.'_'.$UID;
    return base64_encode(hash_hmac('sha1', $base_string, base64_decode(self::getSecret()), true));
  }
  
  /**
    * Generate a password for the facebook user
    *
    * @access public
    * @return string of generated password
    * @param int length of password (default 9)
    * @param int strengh of password (default 4)
    */
  static function generatePassword($length=9, $strength=4) {
    $vowels = 'aeuy';
    $consonants = 'bdghjmnpqrstvz';
    if ($strength & 1) {
        $consonants .= 'BDGHJLMNPQRSTVWXZ';
    }
    if ($strength & 2) {
        $vowels .= "AEUY";
    }
    if ($strength & 4) {
        $consonants .= '23456789';
    }
    if ($strength & 8) {
        $consonants .= '@#$%';
    }

    $password = '';
    $alt = time() % 2;
    for ($i = 0; $i < $length; $i++) {
        if ($alt == 1) {
            $password .= $consonants[(rand() % strlen($consonants))];
            $alt = 0;
        } else {
            $password .= $vowels[(rand() % strlen($vowels))];
            $alt = 1;
        }
    }
    return $password;
  }
  
  /**
    * Give a nice utility method directly to the API datasource
    * This will create the datasource if need be, execute the 
    * method along with params and return the results
    *
    * @param string method name (required)
    * @param array of options
    * @return array of results from Gigya REST API
    * @access public
    */
  static function api($method = null, $params = array()){
    if(!$method){
      return array();
    }
    return self::__getDataSource()->$method($params);
  }
  
  /**
    * Get the Gigya datasource, first attempt to use the already instanciated source
    * otherwise create it, cache it and return it.
    *
    * @return GigyaDataSource Object
    * @access private
    */
  static private function __getDataSource(){
    if(self::$GigyaSource){
      return self::$GigyaSource;
    }
    App::import('Core', 'ConnectionManager');
    self::$GigyaSource = ConnectionManager::getDataSource('gigya');
    return self::$GigyaSource;
  }
}
?>