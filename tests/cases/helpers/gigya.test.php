<?php
/* Gigya Test cases generated on: 2010-05-06 22:05:18 : 1273205238*/
App::import('Helper', 'Gigya.Gigya');
App::import('Helper', 'Js');
App::import('Helper', 'JqueryEngine');
App::import('Helper', 'Html');
App::import('Lib', 'GigyaUtil');
class GigyaHelperTestCase extends CakeTestCase {
	function startTest() {
		$this->Gigya = new GigyaHelper();
		$this->Gigya->Html = new HtmlHelper();
		$this->Gigya->Js = new JsHelper();
		$this->Gigya->Js->JqueryEngine = new JqueryEngineHelper();
		$this->apiKey = GigyaUtil::getApiKey();
		$this->secret = GigyaUtil::getSecret();
	}
	
	function testSetConf(){
	  $this->Gigya->setConf(array('key' => 'value'));
	  $this->assertEqual('{"key":"value"}', $this->Gigya->conf);
	}
	
	function testLoader(){
	  $result = $this->Gigya->loader();
	  $this->assertEqual('{"APIKey":"'.$this->apiKey.'"}', $this->Gigya->conf);
	  $this->assertEqual('<script type="text/javascript" src="http://cdn.gigya.com/JS/socialize.js?apikey=2_ktXFWy_nfk8gJWaISIXppxQ9gdALGdUytuXLnRnknopKrffqnUuhf9psv09xYy1t"></script>', $result);
	}
	
	function testLogin(){
	  $this->Gigya->setConf(array('key' => 'value'));
	  
	  $result = $this->Gigya->login();
	  $expected = '<script type="text/javascript">
//<![CDATA[
gigya.services.socialize.showLoginUI({"key":"value"}, {"redirectURL":"\/gigya\/socialize\/login","enabledProviders":"facebook,myspace,twitter,linkedin,google"})
//]]>
</script>';
	  $this->assertEqual($expected, $result);
	  
	  $result = $this->Gigya->login(array('redirectURL' => array('controller' => 'pages', 'action' => 'home')));
	  $expected = '<script type="text/javascript">
//<![CDATA[
gigya.services.socialize.showLoginUI({"key":"value"}, {"redirectURL":"\/pages\/home","enabledProviders":"facebook,myspace,twitter,linkedin,google"})
//]]>
</script>';
    $this->assertEqual($expected, $result);
	}
	
	function testCallBuffer(){
	  $this->Gigya->setConf(array('key' => 'value'));
	  $result = $this->Gigya->connect(array('buffer' => true));
	  $expected = 'gigya.services.socialize.connect({"key":"value"}, {"buffer":true})';
	  $this->assertEqual($expected, $result);
	}

	function endTest() {
		unset($this->Gigya);
		ClassRegistry::flush();
	}

}
?>