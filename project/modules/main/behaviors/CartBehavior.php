<?php

namespace project\modules\main\behaviors;

use craft\commerce\elements\Donation;
use craft\commerce\elements\Order;
use yii\base\Behavior;

class CartBehavior extends Behavior
{

    public function isDonation()
    {
        /** @var Order $cart */
        $cart = $this->owner;
        if (! $cart->hasLineItems()) {
            return false;
        }

        // There is always only one line item in commerce light edition
        return $cart->lineItems[0]->purchasable instanceof Donation;

    }
}
