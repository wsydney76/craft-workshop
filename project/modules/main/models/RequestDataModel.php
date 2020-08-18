<?php

namespace project\modules\main\models;

use craft\base\Model;

class RequestDataModel extends Model
{
    protected $data = [];

    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function get($key)
    {
        return $this->data[$key] ?? null;
    }
}
