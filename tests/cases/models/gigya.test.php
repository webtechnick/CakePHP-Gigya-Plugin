<?php
/* Gigya Test cases generated on: 2010-05-07 15:05:14 : 1273266434*/
App::import('Model', 'Gigya.Gigya');

class GigyaTestCase extends CakeTestCase {
  var $fixtures = array(
    'plugin.gigya.gigya'
  );
  
	function startTest() {
		$this->Gigya =& ClassRegistry::init('Gigya');
	}
	
	function testFindUserIdByUid(){
	  $result = $this->Gigya->findUserIdByUid('UID');
	  $this->assertEqual(1, $result);
	  
	  $result = $this->Gigya->findUserIdByUid(1);
	  $this->assertEqual(1, $result);
	  
	  $result = $this->Gigya->findUserIdByUid('UIDID');
	  $this->assertEqual(2, $result);
	  
	  $result = $this->Gigya->findUserIdByUid(2);
	  $this->assertEqual(2, $result);
	  
	  $result = $this->Gigya->findUserIdByUid('BOGUSIGNOREME');
	  $this->assertEqual(null, $result);
	}

	function endTest() {
		unset($this->Gigya);
		ClassRegistry::flush();
	}

}
?>