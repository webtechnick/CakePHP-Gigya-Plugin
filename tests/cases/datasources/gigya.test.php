<?php
App::import('Datsource', 'Gigya.GigyaSource');
App::import('Core', 'HttpSocket');
//Mock::generatePartial('GigyaSource', 'MockGigyaSource', array('__request'));
Mock::generatePartial('HttpSocket', 'MockHttpSocket', array('request'));
class GigyaAssociatesTestCase extends CakeTestCase {
  var $Gigya = null;
  
  function startTest(){
    $this->Gigya = new GigyaSource(array());
    $this->Gigya->Http = new MockHttpSocket();
  }
  
  function testCallGetUserInfo(){
    $this->Gigya->Http->expectOnce('request');
    $result = $this->Gigya->getUserInfo(array('uid' => 1));
    
    $this->assertTrue(!empty($this->Gigya->__requestLog[0]['params']['apiKey']));
    $this->assertTrue(!empty($this->Gigya->__requestLog[0]['params']['secret']));
    $this->assertTrue(!empty($this->Gigya->__requestLog[0]['params']['uid']));
    $this->assertEqual('/socialize.getUserInfo', $this->Gigya->__requestLog[0]['url']['path']);
  }
  
  function testCallUnavailableMethod(){
    $this->Gigya->Http->expectNever('request');
    $result = $this->Gigya->getBogusRequest(array('uid' => 1));
    
    $this->assertTrue(empty($this->Gigya->__requestLog));
    $this->assertEqual('getBogusRequest is not a valid method', $this->Gigya->errors[0]);
    $this->assertFalse($result);
  }
  
  function endTest(){
    unset($this->Gigya);
  }
}
?>