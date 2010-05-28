Author: Nick Baker
Requred: PHP 5.1.2+
License: MIT


Install:
========================
- copy the plugin into app/plugins/gigya
- copy gigya/config/gigya.php.default to app/config/gigya.php and fill out the details below.

app/config/gigya.php
$config = array(
  'Gigya' => array(
    'apiKey' => 'GIGYA API KEY',
    'secret' => 'GIGYA SECRET KEY'
  )
);

- run the schema file into you database
cake schema create -plugin gigya

Setup:
========================
You'll need to signup for a free Gigya account and configure all your social networking apps to let gigya handle the connection/login/posting/etc.. callbacks.   Gigya will set up all your apps for free, but it will take ~1 week to have them do it for you.  I suggestion just doing it yourself.  It's easy, and they've written full tutorials on how to do it, complete with screenshots.

Start Here: http://wiki.gigya.com/035_Socialize_Setup 


Usage:
========================
Use the gigya helper to include the gigya socialize login/connect widget within your app

//some controller
var $helpers = array('Gigya.Gigya');


Load required scripts in the head, and use any of the built in gigya helper methods

Example Layout:
========================
//views/layouts/default.ctp
<html>
<head>
  <?= $gigya->loader(); ?>
</head>
<body>
  <?php
  //Pseudo code, check if user is logged in or not, usually via Auth. 
  if(!$user_id_logged_in){
    echo $gigya->login();
  } else {
    $html->link('Logout', array('plugin' => 'gigya', 'controller' => 'socialize', 'action' => 'logout'));
  } 
  ?>
</body>
</html>
 
Customize the look and feel of the login/connect with tons of options, including custom 
callbacks both inline (javascript) or through your CakePHP app (url redirects).
For a full list of available options look at:
http://wiki.gigya.com/030_API_reference/010_Client_API/020_Methods/socialize.showLoginUI

Some useful examples:
========================
//Load the widget in a container (default is a popup)
<div id="login-container"></div>
<?= $gigya->login(array('containerID' => 'login-container')); ?>

//Only allow facebook, twitter, or linkedin logins/connect
<?= $gigya->login(array('enabledProviders' => 'facebook,twitter,linkedin')); ?>

//Allow everything EXCEPT a certain provicer
<?= $gigya->login(array('disabledProviders' => 'myspace')); ?>

//Set height and width and add a style
//styles: standard (default), blue, fullLogo
<?= $gigya->login(array('height' => '300', 'width' => 500', 'buttonsStyle' => 'fullLogo')); ?>


Default Login Action:
========================
By default any login click will be directed to the login action of the plugin.

  NOTE: you can change this behavior by passing in different options in the $gigya->login() function example: 
        $gigya->login(array('redirecURL' => array('controller' => 'gigyas', 'action' => 'custom_login')));


The login action does a few things based on different senarios.

1) If the user is authenticated via AuthComponent, the social network connection will
   be saved to the database and then linked to the Gigya with the user account.
    
2) If the user is not authenticated via AuthComponent, the social network connection will
   authenticate the user using the social network decided, then attempt to create the user
   based on the AuthComponent settings.
   
Login Flow Chart visual representation:
http://www.webtechnick.com/img/gigya_flow_chart_final.jpg
   
At anytime, the developer has access to callback functions in and around the login/connection process.
All callbacks need to be defined in app_controller.php in the main app to work.


Available Callbacks:
========================
/**
* hands the authenticated user in, if the function returns a 
* valid $user_id the internal handle_user function will be
* shortcutted proceeding straight to linking the user_id 
* to Gigya
*
* @param authenticated social network user
* @return mixed user_id or boolean false to proceed
*/
function beforeGigyaLogin($user){
  //return valid user_id or false
}

/**
* Preform some needed logic after a successful login
*
* @param authenticated social network user
* @return void
*/
function afterGigyaLogin($user){
  //Do something with the user if need be.
}

/**
* Preform some needed logic before a logout
*/
function beforeGigyaLogout(){
  //Do something...
}
  
/**
* Allow the developer to decide how to create the user
* instead of the Gigya plugin guessing what to do
* by introspection on the Auth Component
*
* Defining this callback is preferable to the plugin guessing 
* how your users table is constructed.  Although the plugin
* does a good job of creating a valid user for you, its
* always nicer to do it yourself to be sure there are no 
* errors.
*
* @param authenticated gigya user
* @return mixed user_id of created user, or false to let plugin decide.
*/
function gigyaCreateUser($user){
  //create the new user and return the created user_id;
}  
  
Upon a successful login, the user key will be saved and linked to their account.  The benefit of linking the 
user_id to the gigya_uuid is you can use the GigyaApi to make gigya calls based on the user_id and the right thing 
will happen.


Example Usage:
========================
//after a successful connect.
App::import('Lib', 'Gigya.GigyaUtil');
$result = GigyaUtil::api('getUserInfo', array('uid' => $this->Auth->user('id')));
debug($result);

That will output all the social networks your app is allowed to access, based on this information you can do 
multiple things like setStatus or getFriends, or getPhotos, etc..

Setting the status on users social networks.
Using the same Static based: 

App::import('Lib', 'Gigya.GigyaUtil');
$result = GigyaUtil::api('setStatus', array('uid' => $this->Auth->user('id'), 'status' => 'Posting from the Gigya Plugin!'));
