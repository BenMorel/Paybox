<?php

namespace Paybox\Request;

use Paybox\Request;

/**
 * Cancel of a transaction on a subscriber.
 */
class SubscriberCancel implements Request
{
    /**
     * @inheritdoc
     */
    public function getValues()
    {
        return [
            'TYPE' => '00055',
            'REFABONNE' => '',
        ];
    }
}
