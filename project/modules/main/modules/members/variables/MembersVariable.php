<?php

namespace project\modules\main\modules\members\variables;

use craft\base\Component;
use craft\commerce\models\Address;
use craft\elements\User;
use project\modules\main\modules\members\models\LoginData;

class MembersVariable extends Component
{
    public function getLoginData($config)
    {
        return new LoginData($config);
    }

    public function getNewAddress()
    {
        return new Address();
    }

    public function getNewUser()
    {
        return new User();
    }

}
