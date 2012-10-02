<?php
/**
 * @author Shea Dawson <shea@silverstripe.com.au>
 * @license BSD http://silverstripe.org/BSD-license
 */
class RateableService{

	/**
	 * checks to see if a user has already rated this object
	 * by checking against their SessionID or MemberID if logged in
	 * @param String $class DataObject ClassName
	 * @param Int $id DataObject ID
	 * @return Boolean
	 **/
	public function userHasRated($class, $id){
		$ratings = $this->getRatingsFor($class, $id);
		if($ratings->exists()){
			if($ratings->filter('SessionID', session_id())->exists()){
				return true;			
			}	
			if($memberID = Member::currentUserID()){
				if($ratings->filter('MemberID', $memberID)->exists()){
					return true;			
				}	
			}	
		}

		return false;
	}


	/**
	 * gets the rating objects for an object
	 * @param String $class DataObject ClassName
	 * @param Int $id DataObject ID
	 * @return DataList
	 **/
	public function getRatingsFor($class, $id){
		return Rating::get()->filter(array(
			'ObjectClass' => $class,
			'ObjectID' => $id
		));
	}


	/**
	 * takes a DataList of Rateable DataObjects and sorts them by their average score 
	 * @param DataList $list
	 * @return ArrayList
	 **/
	public function sortByRating(DataList $list, $dir = 'DESC'){
		$items = new ArrayList($list->toArray());
		foreach ($items as $item) {
			$score = $item->getAverageScore();
			$item->Score = $score ? $score : 0;
			$item->Title = $item->Title;
		}

		return $items->sort('Score', $dir);
	}
}