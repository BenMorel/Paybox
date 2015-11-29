<?php

namespace Paybox\Request;

use Paybox\Request;

/**
 * Checks the status of a transaction.
 */
class Inquire implements Request
{
    /**
     * @var string
     */
    private $numtrans;

    /**
     * Inquire constructor.
     *
     * @param string $numtrans A transaction number returned in a Response.
     */
    public function __construct($numtrans)
    {
        $this->numtrans = $numtrans;
    }

    /**
     * @inheritdoc
     */
    public function getValues()
    {
        return [
            'TYPE'     => '00017',
            'NUMTRANS' => $this->numtrans,
        ];
    }
}
