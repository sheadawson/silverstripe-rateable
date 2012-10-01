<?php

define('RATEABLE_MODULE', 'rateable');

if (basename(dirname(__FILE__)) != RATEABLE_MODULE) {
	throw new Exception(RATEABLE_MODULE . ' module not installed in correct directory');
}

Director::addRules(100, array(
	RateableController::URLSegment . '//$Action/$ObjectClassName/$ObjectID/$Rating' => 'RateableController'
));