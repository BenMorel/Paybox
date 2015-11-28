<?php

namespace Paybox\Request;

use Paybox\Request;

/**
 * Update the amount of a transaction.
 */
class UpdateTransactionAmount implements Request
{
    /**
     * @inheritdoc
     */
    public function getValues()
    {
        return [
            'TYPE' => '00013',
            'MONTANT' => '',
            'DEVISE' => '',
            'REFERENCE' => '',
            'NUMAPPEL' => '',
            'NUMTRANS' => '',

            // optional
//            'AUTORISATION' => '',
        ];
    }
}
