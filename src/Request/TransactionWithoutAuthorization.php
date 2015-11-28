<?php

namespace Paybox\Request;

use Paybox\Request;

/**
 * Transaction without authorization request.
 */
class TransactionWithoutAuthorization implements Request
{
    /**
     * @inheritdoc
     */
    public function getValues()
    {
        return [
            'TYPE' => '00012',
            'MONTANT' => '',
            'DEVISE' => '',
            'REFERENCE' => '',
            'PORTEUR' => '',
            'DATEVAL' => '',
            'CVV' => '',
        ];
    }
}
