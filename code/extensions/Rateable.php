<?php
/**
 * @author Shea Dawson <shea@silverstripe.com.au>
 * @license BSD http://silverstripe.org/BSD-license
 */
class Rateable extends DataExtension {

	public static $dependencies = array(
		'rateableService'	=> '%$RateableService',
	);

	
	/**
	 * @var RateableService
	 */
	public $rateableService;


	/**
	 * gets the average rating score
	 * @return Int
	 **/
	public function getAverageScore(){
		return $this->rateableService->getRatingsFor($this->owner->ClassName, $this->owner->ID)->avg('Score');
	}


	/**
	 * checks to see if the current user has rated this object
	 * by checking against the rating SessionID and MemberID
	 * @return Boolean
	 **/
	public function UserHasRated(){
		return $this->rateableService->userHasRated($this->owner->ClassName, $this->owner->ID);
	}


	/**
	 * returns the JS and HTML required for the star rating UI
	 * @return String
	 **/
	public function RateableUI(){
		Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
		Requirements::javascript(RATEABLE_MODULE . '/javascript/jquery.raty.js');
		return $this->owner->renderWith('RateableUI');
	}


	/**
	 * returns a unique HTML ID for each RateableUI div
	 * @return String
	 **/
	public function getRatingHTMLID(){
		return $this->owner->ClassName . '-' . $this->owner->ID . '-' . 'rating';
	}
	

	/**
	 * return the url path for rating this object
	 * @return String
	 **/
	public function RatePath(){
		return Controller::join_links(RateableController::URLSegment, 'rate', $this->owner->ClassName, $this->owner->ID);
	}
}
