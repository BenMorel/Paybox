<?php

namespace Paybox\Request;

use Paybox\Request;

/**
 * Inquiry.
 */
class Inquire implements Request
{
    /**
     * @inheritdoc
     */
    public function getValues()
    {
        return [
            'TYPE' => '00017',
            'NUMTRANS' => '',
        ];
    }
}
