<?php

namespace Paybox\Request;

use Paybox\Request;

/**
 * Update an existing subscriber.
 */
class SubscriberUpdate implements Request
{
    /**
     * @inheritdoc
     */
    public function getValues()
    {
        return [
            'TYPE' => '00057',
            'REFABONNE' => '',
        ];
    }
}
