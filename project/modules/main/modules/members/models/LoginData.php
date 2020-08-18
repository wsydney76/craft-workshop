<?php

namespace project\modules\main\modules\members\models;

use craft\base\Model;

class LoginData extends Model
{
    public $loginName = '';
    public $password = '';
    public $newPassword = '';
    public $rememberMe = 0;

}
