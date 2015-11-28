<?php

namespace Paybox\Request;

use Paybox\Request;

/**
 * Inquiry.
 */
class Inquiry implements Request
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
