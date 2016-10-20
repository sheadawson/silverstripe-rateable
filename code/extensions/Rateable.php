<?php
/**
 * @author Shea Dawson <shea@silverstripe.com.au>
 * @license BSD http://silverstripe.org/BSD-license
 */
class Rateable extends DataExtension
{
    private static $dependencies = array(
        'rateableService'    => '%$RateableService',
    );

    /**
     * Templates to render with.
     *
     * @var array
     */
    private static $rateable_templates = array();

    /**
     * The maximum score a user can rate this item
     *
     * @var array
     */
    private static $rateable_rating_max = 5;

    /**
     * If true, rateable will always be turned on regardless of 'EnableRatings'.
     * The field will also no longer be available in the CMS as it's made redundant.
     *
     * @var boolean
     */
    private static $rateable_config_enabled = false;

    /**
     * If true, the user can change their rating at any time.
     *
     * @var boolean
     */
    private static $rateable_can_change_rating = false;
    
    /**
     * @var RateableService
     */
    public $rateableService;

    /**
     * @var String
     */
    private $htmlIdPostfix;

    /** 
     * Setting up DB / has_one / defaults with "get_extra_config" allows you to extend
     * an extension class without breaking the $db configs.
     *
     * @return array
     */
    public static function get_extra_config($class, $extension, $args) {
        return array(
            'db' => array('EnableRatings' => 'Boolean'),
            'defaults' => array('EnableRatings' => '1'),
        );
    }

    public function updateSettingsFields(FieldList $fields)
    {
        if (!$this->owner->config()->rateable_config_enabled) {
            $fields->addFieldToTab('Root.Settings', CheckboxField::create('EnableRatings', _t('Rateable.db_EnableRatings', 'Enable Ratings')));
        }
    }


    public function updateCMSFields(FieldList $fields)
    {
        if (!is_subclass_of($this->owner, 'SiteTree') && !$this->owner->config()->rateable_config_enabled) {
            $fields->addFieldToTab('Root.Main', CheckboxField::create('EnableRatings', _t('Rateable.db_EnableRatings', 'Enable Ratings')));
        }
    }

    /**
     * gets the average rating score
     * @return Int
     **/
    public function getAverageScore()
    {
        return $this->rateableService->getRatingsFor($this->owner->ClassName, $this->owner->ID)->avg('Score');
    }

    /**
     * Get the available ratings.
     *
     * @return ArrayList
     */
    public function getRatingOptions()
    {
        $averageScoreRoundedUp = ceil($this->getAverageScore());
        $maxRating = $this->owner->getMaxRating();

        $result = array();
        for ($i = 1; $i <= $maxRating; ++$i) {
            $result[] = new ArrayData(array(
                'Score'    => (int)$i,
                'IsAverageScore' => ($i <= $averageScoreRoundedUp)
            ));
        }
        return new ArrayList($result);
    }

    /**
     * gets the number of ratings
     * @return int
     */
    public function getNumberOfRatings()
    {
        return (int)$this->rateableService->getRatingsFor($this->owner->ClassName, $this->owner->ID)->count();
    }

    /**
     * Get the maximum rating
     *
     * @return int
     */
    public function getMaxRating() 
    {
        return $this->owner->config()->rateable_rating_max;
    }

    /**
     * checks to see if the current user has rated this object
     * by checking against the rating SessionID and MemberID
     * @return Boolean
     **/
    public function UserHasRated()
    {
        return $this->rateableService->userHasRated($this->owner->ClassName, $this->owner->ID);
    }

    /**
     * returns the JS and HTML required for the star rating UI
     * @var $htmlIdPostfix String - appends a given unique identifier to the ratingHTMLID. This allows 
     * multiple instances of the same ratable object on one page
     * @return String
     **/
    public function RateableUI($htmlIdPostfix = false)
    {
        if (!$this->owner->checkRatingsEnabled()) {
            return;
        }

        $this->htmlIdPostfix = $htmlIdPostfix;

        Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
        Requirements::javascript(RATEABLE_MODULE . '/javascript/rateable.min.js');
        Requirements::css(RATEABLE_MODULE . '/css/rateable.min.css');
    
        $templates = $this->owner->stat('rateable_templates');
        $templates[] = 'RateableUI';
        return $this->owner->renderWith($templates);
    }


    /**
     * returns a unique HTML ID for each RateableUI div
     * @return String
     **/
    public function getRatingHTMLID()
    {
        $parts = array(
            $this->owner->ClassName,
            $this->owner->ID,
            'rating'
        );

        if ($this->htmlIdPostfix) {
            $parts[] = $this->htmlIdPostfix;
        }

        return implode('-', $parts);
    }


    /**
     * returns a string to be used in the RatableUI's css class attribute
     * @return String
     **/
    public function getRatingCSSClass()
    {
        $parts = array(
            'rateable-ui'
        );

        if ($this->UserHasRated()) {
            $parts[] = 'has-voted';
        }

        return implode(' ', $parts);
    }


    /**
     * Checks whether ratings should be enabled on this object
     * @return Boolean
     **/
    public function checkRatingsEnabled()
    {
        $enableRatings = ($this->owner->EnableRatings || $this->owner->config()->rateable_config_enabled);
        return $enableRatings && $this->owner->exists() && $this->owner->ClassName != 'ErrorPage';
    }
    
    /**
     * Check whether the user can take back a rating or not.
     *
     * @return boolean
     */
    public function canChangeRating() {
        return (int)$this->owner->config()->rateable_can_change_rating;
    }

    /**
     * return the url path for rating this object
     * @return String
     **/
    public function RatePath()
    {
        return Controller::join_links(RateableController::URLSegment, 'rate', $this->owner->ClassName, $this->owner->ID);
    }
}
