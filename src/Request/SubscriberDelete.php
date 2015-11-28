<?php

namespace Paybox\Request;

use Paybox\Request;

/**
 * Delete a subscriber.
 */
class SubscriberDelete implements Request
{
    /**
     * @inheritdoc
     */
    public function getValues()
    {
        return [
            'TYPE' => '00058',
        ];
    }
}
