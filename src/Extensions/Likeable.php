<?php

namespace SheaDawson\Rateable\Extensions;

class Likeable extends Rateable
{
    /**
     * {@inheritdoc}
     */
    private static $rateable_rating_max = 1;

    /**
     * {@inheritdoc}
     */
    private static $rateable_can_change_rating = true;

    /**
     * {@inheritdoc}
     */
    private static $rateable_templates = array('Includes/LikeableUI');
}
