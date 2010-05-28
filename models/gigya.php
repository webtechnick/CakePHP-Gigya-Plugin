<?php
/**
  * Model to hold gigya uuid's mapped to user_ids.
  *
  * @author Nick Baker
  * @version 0.1
  * @license MIT
  */
class Gigya extends GigyaAppModel {
	var $name = 'Gigya';
	var $displayField = 'gigya_uid';
	
	/**
	  * Return the user_id from a given uid
	  * This is used to check if a user has been
	  * the UID can either be the gigya uid 
	  * or it can be the actual user_id for 
	  * repeat users
	  *
	  * @param mixed uid
	  * @return mixed user_id of Auth user
	  * @access public
	  */
	function findUserIdByUid($uid = null){
	  return $this->field('user_id', array(
	    'OR' => array(
	      'gigya_uid' => $uid,
	      'user_id' => $uid
	    )
	  ));
	}
}
?>