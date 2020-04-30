<?php

namespace SheaDawson\Rateable\Controllers;

use SheaDawson\Rateable\Model\Rating;
use SheaDawson\Rateable\Services\RateableService;
use SilverStripe\Control\Controller;

/**
 * @author Shea Dawson <shea@silverstripe.com.au>
 * @license BSD http://silverstripe.org/BSD-license
 */
class RateableController extends Controller
{

    const URLSegment = 'rateable';

    private static $dependencies = array(
        'rateableService'    => '%$RateableService',
    );

    private static $allowed_actions = array(
        'rate'
    );

    /**
     * @var RateableService
     */
    public $rateableService;


    /**
     * action for rating an object
     * @return String JSON
     **/
    public function rate($request)
    {
        $class  = str_ireplace('-', '\\', $request->param('ObjectClassName'));
        $id     = (int)$request->param('ObjectID');
        $score  = (int)$request->getVar('score');

        // check we have all the params
        if (!class_exists($class) || !$id || !$score || (!$object = $class::get()->byID($id))) {
            return json_encode(array(
                'status' => 'error',
                'message' => _t('RateableController.ERRORMESSAGE', 'Sorry, there was an error rating this item')
            ));
        }

        // check the object exists
        if (!$object || !$object->checkRatingsEnabled()) {
            return json_encode(array(
                'status' => 'error',
                'message' => _t('RateableController.ERRORNOTFOUNT', 'Sorry, the item you are trying to rate could not be found')
            ));
        }

        // check the user can rate the object
        $ratingRecord = $this->rateableService->userGetRating($class, $id);
        if ($ratingRecord) {
            if (!$object->canChangeRating()) {
                return json_encode(array(
                    'status' => 'error',
                    'message' => _t('RateableController.ERRORALREADYRATED', 'Sorry, You have already rated this item')
                ));
            }

            // If clicked same score as before, remove rating
            if ($score == $ratingRecord->Score)
            {
                // Remove rating
                $ratingRecord->delete();

                // Success
                return json_encode(array(
                    'status' => 'success',
                    'isremovingrating' => 1,
                    'averagescore' => $object->getAverageScore(),
                    'numberofratings' => $object->getNumberOfRatings(),
                    'message' => _t('RateableController.RATINGREMOVED', 'Your rating has been removed!')
                ));
            }
        }

        // check if score is valid
        $isScoreValid = false;
        $scoreOptions = $object->getRatingOptions();
        if ($scoreOptions) {
            foreach ($scoreOptions as $scoreOption) {
                $isScoreValid = ($isScoreValid || ($score == $scoreOption->Score));
            }
        }
        if (!$isScoreValid) {
            return json_encode(array(
                'status' => 'error',
                'message' => _t('RateableController.ERRORINVALIDRATING', 'You sent an invalid rating.')
            ));
        }

        // create the rating
        $isRatingNew = (!$ratingRecord);
        if (!$ratingRecord) {
            $ratingRecord = Rating::create(array(
                'ObjectID'        => $id,
                'ObjectClass'    => $class
            ));
        }
        $ratingRecord->Score = $score;
        $ratingRecord->write();

        // success
        return json_encode(array(
            'status' => 'success',
            'isnew'  => $isRatingNew,
            'averagescore' => $object->getAverageScore(),
            'numberofratings' => $object->getNumberOfRatings(),
            'message' => ($isRatingNew) ? _t('RateableController.THANKYOUMESSAGE', 'Thanks for rating!') :  _t('RateableController.CHANGEMESSAGE', 'Your rating has been changed!')
        ));
    }
}
