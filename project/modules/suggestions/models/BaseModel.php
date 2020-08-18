<?php

namespace project\modules\suggestions\models;

use craft\base\Model;

class BaseModel extends Model
{

    private $_oldAttributes = [];

    public function setSavePoint()
    {
        $this->_oldAttributes = $this->getAttributes();
    }

    public function hasChangedAttributes(array $attributes)
    {
        if (!$this->_oldAttributes) {
            return false;
        }

        foreach ($attributes as $attribute) {
            if ($this->$attribute != $this->_oldAttributes[$attribute]) {
                return true;
            }
        }

        return false;
    }

}
