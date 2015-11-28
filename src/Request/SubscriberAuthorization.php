<?php

namespace Paybox\Request;

use Paybox\Request;

/**
 * Authorization only on a subscriber.
 */
class SubscriberAuthorization implements Request
{
    /**
     * @inheritdoc
     */
    public function getValues()
    {
        return [
            'TYPE' => '00051',
            'MONTANT' => '',
            'DEVISE' => '',
            'REFERENCE' => '',
            'REFABONNE' => '',
        ];
    }
}
