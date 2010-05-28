<?php
/* Socialize Test cases generated on: 2010-05-06 18:05:13 : 1273192873*/
App::import('Controller', 'Gigya.Socialize');
App::import('Component', 'Auth');
App::import('Model', 'User');
class TestSocializeController extends SocializeController {
	var $autoRender = false;
	var $redirectUrl = null;

	function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}

Mock::generatePartial('TestSocializeController', 'MockTestSocializeController', array('__linkAccount'));
Mock::generate('AuthComponent');
class SocializeControllerTestCase extends CakeTestCase {
  var $fixtures = array(
    'plugin.gigya.gigya',
    'app.user'
  );
  
	function startTest() {
		$this->Socialize = new MockTestSocializeController();
		$this->Socialize->Gigya = ClassRegistry::init('Gigya.Gigya');
		$this->Socialize->Auth = new MockAuthComponent();
		$this->count = $this->Socialize->Gigya->find('count');
	}
	
	function testHandleUserThatAlreadyExists(){
	  $this->Socialize->setReturnValue('__linkAccount', true);
	  $this->Socialize->expectNever('__linkAccount', array(1, 1));
	  $this->assertTrue($this->Socialize->__handleUser(array('UID' => 1)));
	  $this->assertTrue($this->Socialize->__handleUser(array('UID' => 'UID')));
	}
	
	function testHandleUserExistsButNotLinked(){
	  $this->Socialize->Auth->setReturnValue('user', 4);
	  $this->Socialize->expectOnce('__linkAccount', array(4, 'NEWUUID'));
	  $this->assertEqual(4, $this->Socialize->__handleUser(array('UID' => 'NEWUUID')));
	  
	  $result = $this->Socialize->Gigya->findById($this->Socialize->Gigya->id);
	  $this->assertEqual($this->count + 1, $this->Socialize->Gigya->find('count'));
	  $this->assertEqual(4, $result['Gigya']['user_id']);
	  $this->assertEqual('NEWUUID', $result['Gigya']['gigya_uid']);
	}
	
	function testHandleUserShouldCreateUserIfNotFoundWithUsername(){
	  $User = ClassRegistry::init('User');
	  $users = $User->find('count');
	  $this->Socialize->Auth->setReturnValue('user', null);
	  $this->Socialize->Auth->setReturnValue('password', 'newpassword');
	  $this->Socialize->Auth->setReturnValue('getModel', $User);
	  $this->Socialize->Auth->fields = array(
	    'username' => 'username',
	    'password' => 'password'
	  );
	  $this->Socialize->expectOnce('__linkAccount', array('6', 'NEWUUID'));
	  $this->assertEqual(6, $this->Socialize->__handleUser(array('UID' => 'NEWUUID', 'email' => 'email@example.com', 'nickname' => 'nickname')));
	  
	  $this->assertEqual($users + 1, $User->find('count'));
	}
	
	function testHandleUserShouldCreateUserIfNotFoundWithEmail(){
	  $User = ClassRegistry::init('User');
	  $users = $User->find('count');
	  $gigyas = $this->Socialize->Gigya->find('count');
	  $this->Socialize->Auth->setReturnValue('user', null);
	  $this->Socialize->Auth->setReturnValue('password', 'newpassword');
	  $this->Socialize->Auth->setReturnValue('getModel', $User);
	  $this->Socialize->Auth->fields = array(
	    'username' => 'email',
	    'password' => 'password'
	  );
	  $this->Socialize->expectOnce('__linkAccount', array('6', 'NEWUUID'));
	  $this->assertEqual(6, $this->Socialize->__handleUser(array('UID' => 'NEWUUID', 'email' => 'email@example.com', 'nickname' => 'nickname')));
	  
	  $this->assertEqual($users + 1, $User->find('count'));
	  $results = $User->findById(6);
	  $this->assertEqual('email@example.com', $results['User']['email']);
	  $results = $this->Socialize->Gigya->findById($this->Socialize->Gigya->id);
	  
	  $this->assertEqual(6, $results['Gigya']['user_id']);
	  $this->assertEqual('NEWUUID', $results['Gigya']['gigya_uid']);
	  $this->assertEqual($gigyas + 1, $this->Socialize->Gigya->find('count'));
	}

	function endTest() {
		unset($this->Socialize);
		ClassRegistry::flush();
	}

}
?>