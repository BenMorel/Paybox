<?php

namespace Paybox\Request;

use Paybox\Request;

/**
 * Debit (Capture) on a subscriber.
 */
class SubscriberCapture implements Request
{
    /**
     * @inheritdoc
     */
    public function getValues()
    {
        return [
            'TYPE' => '00052',
            'MONTANT' => '',
            'DEVISE' => '',
        ];
    }
}
