<?php
/**
  * Socialize Controller to handle the login/logout and various other actions needed
  * by Gigya to integrate into CakePHP Auth.
  *
  * @author Nick Baker
  * @version 0.1
  * @license MIT
  */
App::import('Lib', 'Gigya.GigyaUtil');
class SocializeController extends GigyaAppController{
  
  /**
    * Load the Gigya helper
    */
  var $helpers = array('Gigya.Gigya');
  
  /**
    * Use the Gigya Model
    */
  var $uses = array('Gigya.Gigya');
  
  /**
    * Before filter for controllers, allow each action.
    */
  function beforeFilter(){
    if(isset($this->Auth)){
      $this->Auth->allow('*');
    }
  }
  
  /**
    * Parse the successful passed in user.
    * the logged in user is passed in by the url
    *
    * To give the user control over how the user is
    * created we provide a callback beforeGigyaLogin
    * the user may create in their app_controller.php
    * and passed into it is a the logged in user.
    * 
    * If the beforeGigyaLogin function returns a user_id that 
    * passes a simple boolean check, the internal __handleUser()
    * function will be bypassed going straight to __linkAccount() 
    * that will link the gigya user to the user_id for future use
    */
  function login(){
    $user = $this->__parseUser();
    
    if($this->__validateUser($user)){
      $user_id = $this->__runCallback('beforeGigyaLogin', $user);
      if(!$user_id){
        $user_id = $this->__handleUser($user);
      }
      else {
        $this->__linkAccount($user_id, $user['UID']);
      }
      $this->__loginUser($user_id);
      $this->__runCallback('afterGigyaLogin', $user);
    }
    
    $this->redirect('/');
  }
  
  /**
    * Logout action that will handle logout for the user
    * 
    */
  function logout(){
    $this->__runCallback('beforeGigyaLogout');
    $this->Auth->logout();
  }
  
  /**
    * Validate the signature returned by gigya
    * 
    * @param array of logged in user
    * @return boolean true if validates, false if not
    */
  function __validateUser($user){
    return (GigyaUtil::generateSignature($user['timestamp'], $user['UID']) == $user['signature']);
  }
  
  /**
    * Run the callback if it exists
    * @param string callback
    * @param mixed passed in variable (optional)
    * @return mixed result of the callback function
    */ 
  function __runCallback($callback, $passedIn = null){
    if(is_callable(array($this, $callback))){
      if($passedIn === null){
        return $this->$callback();
      }
      else {
        return $this->$callback($passedIn);
      }
    }
    return false;
  }
  
  /**
    * Parse the user out of the URL string
    * @return array of user
    */
  function __parseUser(){
    return $this->params['url'];
  }
  
  /**
    * Create or Update the logged in user. Create user if need be,
    * link the account to the user account.
    *
    * This will first look to see if we've already made the connection
    * between gigya user and CakePHP user.  If so, we do nothing more
    * and return true
    *
    * If we haven't made the link between gigya user and CakePHP user
    * and the user is currently logged in, we will make the connection
    * save it, and return true
    * 
    * If we're not logged in and haven't made a previous connection
    * with this social network, we must assume this is a new user
    * as such we will create the user based on the social network
    * generate a password and then link the user to the account
    * 
    * @param array of user
    * @return mixed user_id if success, false if failure
    */
  function __handleUser($user){
    $user_id = $this->Gigya->findUserIdByUid($user['UID']);
    if($user_id){
      //this user has logged in before, we have a user ID based on it
      //so we're finished.
      return $user_id;
    }
    
    $user_id = $this->Auth->user('id');
    if($user_id){
      //we're logged in, and this is a new social network login.  
      //Create the link in database and then link it via Gigya Link it.
      $this->__createLinkBetweenUserAndGigya($user_id, $user['UID']);
      $this->__linkAccount($user_id, $user['UID']);
      return $user_id;
    }
    
    $user_id = $this->__runCallback('gigyaCreateUser', $user);
    if($user_id){
      //User creation process has been handled by the developer
      $this->__createLinkBetweenUserAndGigya($user_id, $user['UID']);
      $this->__linkAccount($user_id, $user['UID']);
      return $user_id;
    }
    
    if(!$user_id){
      //If we're here we need to create the user based on what we read from Auth.
      //Ideally, this should be handled by the developber but we'll give it our
      //best guess by reading the Auth component to create the user.
      if(strtolower($this->Auth->fields['username']) == 'email'){
        $username = empty($user['email']) ? 'no_email@example.com' : $user['email'];
      }
      else {
        $username = $user['nickname'];
      }
      $user_data = array(
        $this->Auth->fields['username'] => $username,
        $this->Auth->fields['password'] => $this->Auth->password(GigyaUtil::generatePassword())
      );
      $UserModel = $this->Auth->getModel();
      if($UserModel->save($user_data)){
        $user_id = $UserModel->id;
        $this->__createLinkBetweenUserAndGigya($user_id, $user['UID']);
        $this->__linkAccount($user_id, $user['UID']);
        return $user_id;
      }
    }
    
    return false;
  }
  
  /**
    * Login the user via user_id
    *
    * @param mixed user id
    * @return void
    */
  function __loginUser($user_id){
    $UserModel = $this->Auth->getModel();
    $UserModel->recursive = -1;
    $user = $UserModel->findById($user_id);
    $this->Auth->login($user);
  }
  
  /**
    * Create the link between the user and Gigya on a local database level
    * this is useful so we limit the amount of API callbacks we make to Gigya
    * @param int cakephp user_id (UID or int)
    * @param array user with UID as
    */
  function __createLinkBetweenUserAndGigya($user_id, $UID){
    $data = array(
      'gigya_uid' => $UID,
      'user_id' => $user_id
    );
    return $this->Gigya->save($data);
  }
  
  /**
    * Run the API to link the accounts to Gigya
    *
    * @param CakePHP user_id
    * @param gigya UID
    * @return array of result of attempt for linking.
    */
  function __linkAccount($user_id, $UID){
    return GigyaUtil::api('linkAccounts', array('siteUID' => $user_id, 'uid' => $UID));
  }
}
?>