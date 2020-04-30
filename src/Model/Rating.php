<?php

namespace SheaDawson\Rateable\Model;

use SilverStripe\Control\Controller;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;

/**
 * @author Shea Dawson <shea@silverstripe.com.au>
 * @license BSD http://silverstripe.org/BSD-license
 */
class Rating extends DataObject
{
    private static $table_name = 'Rating';

    private static $db = array(
        'Score'        => 'Int',
        'ObjectID'        => 'Int',
        'ObjectClass'    => 'Varchar',
        'SessionID'    => 'Varchar(255)'
    );

    private static $has_one = array(
        'Member' => Member::class
    );

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        // ensure the current request has a session
        $request = Controller::curr()->getRequest();
        $request->getSession();

        if (session_id()) {
            $this->SessionID = session_id();
        }

        $this->MemberID = Security::getCurrentUser() ? Security::getCurrentUser()->ID : 0;
    }
}
