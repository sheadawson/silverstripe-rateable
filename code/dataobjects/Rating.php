<?php
/**
 * @author Shea Dawson <shea@silverstripe.com.au>
 * @license BSD http://silverstripe.org/BSD-license
 */
class Rating extends DataObject {
	
	private static $db = array(
		'Score' 		=> 'Int',	
		'ObjectID' 		=> 'Int',	
		'ObjectClass' 	=> 'Varchar',
		'SessionID' 	=> 'Varchar(255)'	
	);

	private static $has_one = array(
		'Member' => 'Member'
	);

	public function onBeforeWrite(){
		parent::onBeforeWrite();
		$this->MemberID = Member::currentUserID();
		$this->SessionID = session_id();
	}
}
