<?php
/* Gigya Fixture generated on: 2010-05-07 15:05:04 : 1273266424 */
class GigyaFixture extends CakeTestFixture {
	var $name = 'Gigya';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'gigya_uid' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'user_id' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'gigya_uid' => 'UID',
			'user_id' => '1',
			'created' => '2010-05-07 15:07:04'
		),
		array(
			'id' => 2,
			'gigya_uid' => 'UIDID',
			'user_id' => '2',
			'created' => '2010-05-07 15:07:04'
		),
	);
}
?>