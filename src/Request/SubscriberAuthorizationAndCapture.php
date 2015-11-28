<?php

namespace Paybox\Request;

use Paybox\Request;

/**
 * Authorization + Capture on a subscriber.
 */
class SubscriberAuthorizationAndCapture implements Request
{
    /**
     * @inheritdoc
     */
    public function getValues()
    {
        return [
            'TYPE' => '00053',
            'MONTANT' => '',
            'DEVISE' => '',
            'REFERENCE' => '',
            'REFABONNE' => '',
        ];
    }
}
