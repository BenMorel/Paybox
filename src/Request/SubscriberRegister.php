<?php

namespace Paybox\Request;

use Paybox\Request;

/**
 * Register new subscriber.
 */
class SubscriberRegister implements Request
{
    /**
     * @inheritdoc
     */
    public function getValues()
    {
        return [
            'TYPE' => '00056',
            'REFABONNE' => '',
        ];
    }
}
