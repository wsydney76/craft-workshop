<?php

namespace project\modules\main\variables;

use craft\base\Component;
use craft\helpers\Json;

class GdprVariable extends Component
{
    public function getCookies()
    {
        return $_COOKIE;
    }

    public function hasCookieConsent()
    {
        return array_key_exists('CookieShow', $this->getCookies());
    }

    public function  getCookiePreferences()
    {
        $preferences = [];
        $cookies = $this->getCookies();

        if (array_key_exists('CookiePreferences', $cookies)) {
            $preferences = Json::decode($cookies['CookiePreferences']);
        }

        return $preferences;
    }

    public function hasCookiePreference($preference) {
        $preferences = $this->getCookiePreferences();

        return in_array($preference, $preferences) ;

    }
}
