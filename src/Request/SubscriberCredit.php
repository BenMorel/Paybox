<?php

namespace Paybox\Request;

use Paybox\Request;

/**
 * Credit on a subscriber.
 */
class SubscriberCredit implements Request
{
    /**
     * @inheritdoc
     */
    public function getValues()
    {
        return [
            'TYPE' => '00054',
            'MONTANT' => '',
            'DEVISE' => '',
            'REFERENCE' => '',
            'REFABONNE' => '',
        ];
    }
}
