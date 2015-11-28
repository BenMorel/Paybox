<?php

namespace Paybox\Request;

use Paybox\Request;

/**
 * Force transaction without authorization.
 */
class ForceTransactionWithoutAuthorization implements Request
{
    /**
     * @inheritdoc
     */
    public function getValues()
    {
        return [
            'TYPE' => '00061',
            'MONTANT' => '',
            'DEVISE' => '',
        ];
    }
}
