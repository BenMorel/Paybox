<?php

namespace Paybox\Request;

use Paybox\Request;

/**
 * Refund.
 */
class Refund implements Request
{
    /**
     * @inheritdoc
     */
    public function getValues()
    {
        return [
            'TYPE' => '00014',
            'MONTANT' => '',
            'DEVISE' => '',
        ];
    }
}
