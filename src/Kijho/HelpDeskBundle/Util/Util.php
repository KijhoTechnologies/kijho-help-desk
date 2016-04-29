<?php

namespace Kijho\HelpDeskBundle\Util;

/**
 * Description of Util
 * @author Cesar Giraldo - <cnaranjo@kijho.com> 29/04/2016
 */
class Util {

    public static function getCurrentDate() {
        $datetime = new \DateTime('now');
        return $datetime;
    }
}

