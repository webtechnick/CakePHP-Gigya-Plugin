Author: Nick Baker
Requred: PHP 5.1.2+
License: MIT


Install:
========================
copy gigya/config/gigya.php.default to app/config/gigya.php and fill out the details below.

app/config/gigya.php
$config = array(
  'Gigya' => array(
    'apiKey' => 'GIGYA API KEY',
    'secret' => 'GIGYA SECRET KEY'
  )
);

Setup:
========================
Signup for a free Gigya account and configure all your social network apps to let gigya
handle the connection/login/posting/etc...
http://www.gigya.com/


Usage:
========================
Use the gigya helper to include the gigya socialize login/connect widget within your app

//some controller
var $helpers = array('Gigya.Gigya');


Load required scripts in the head, and use any of the built in gigya helper methods

Example:
========================
 views/layouts/default.ctp
 <?= $gigya->loader(); ?>
 <?= $gigya->login(); ?>
 
Customize the look and feel of the login/connect with tons of options, including custom 
callbacks both inline (javascript) or through your CakePHP app (url redirects).
For a full list of available options look at:
http://wiki.gigya.com/030_API_reference/010_Client_API/020_Methods/socialize.login

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
   
At anytime, the developer has access to callback functions in and around the login/connection process.
All callbacks need to be defined in app_controller.php in the main app to work.


Available Callbacks:
========================
beforeGigyaLogin($user) //needs to return a $user_id
  //handles the authenticated user in, if the function returns a valid $user_id
  //the internal handle_user action will be shortcutted and it will proceed straight to linking the user_id
  //to the gigya account.
  
afterGigyaLogin($user)
  //preform some action after a successful login
  
beforeGigyaLogout()
  //preform some needed logic before the logout process.
  
gigyaCreateUser($user) //needs to return a $user_id
  //preform the logic to actually create a new user.  This lets the developer overwrite the guesswork nand
  //introspection the plugin takes to create a new user account.
  
  
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
