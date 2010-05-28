<?php
App::import('Lib', 'Gigya.GigyaUtil');
Mock::generatePartial('GigyaUtil', 'MockGigyaUtil', array('__getDataSource'));

class GigyaUtilTestCase extends CakeTestCase {
  var $GigyaUtil = null;
  
  function startTest(){
    $this->GigyaUtil = new MockGigyaUtil();
  }
  
  function testGenerateSignature(){
    $result = $this->GigyaUtil->generateSignature('2010-08-14 11:15:55', '_UID');
    $this->assertEqual('nElVKN8pA3mUDfF9N0j3DWCfSSQ=', $result);
  }
  
  function testVersion(){
    $result = $this->GigyaUtil->version();
    $this->assertTrue(!empty($result));
  }
  
  function testDescription(){
    $result = $this->GigyaUtil->description();
    $this->assertTrue(!empty($result));
  }
  
  function testAuthor(){
    $result = $this->GigyaUtil->author();
    $this->assertTrue(!empty($result));
  }
  
  function testGetApiKey(){
    $result = $this->GigyaUtil->getApiKey();
    $this->assertTrue(!empty($result));
  }
  
  function testGetSecret(){
    $result = $this->GigyaUtil->getSecret();
    $this->assertTrue(!empty($result));
  }
  
  function endTest(){
    unset($this->GigyaUtil);
  }
}
?>