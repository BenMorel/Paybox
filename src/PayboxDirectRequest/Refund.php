<?php

namespace Paybox\PayboxDirectRequest;

use Paybox\PayboxDirectRequest;
use Brick\Money\Money;

/**
 * Refunds a captured transaction.
 */
class Refund implements PayboxDirectRequest
{
    /**
     * @var Money
     */
    private $amount;

    /**
     * @var string
     */
    private $numappel;

    /**
     * @var string
     */
    private $numtrans;

    /**
     * Refund constructor.
     *
     * @param Money  $amount    The amount to refund, less than or equal to the captured amount.
     * @param string $numappel  The value returned by Capture or AuthorizeAndCapture.
     * @param string $numtrans  The value returned by Capture or AuthorizeAndCapture.
     */
    public function __construct(Money $amount, $numappel, $numtrans)
    {
        $this->amount    = $amount;
        $this->numappel  = $numappel;
        $this->numtrans  = $numtrans;
    }

    /**
     * @inheritdoc
     */
    public function getValues()
    {
        return [
            'TYPE'     => '00014',
            'MONTANT'  => $this->amount->getMinorAmount()->toInt(),
            'DEVISE'   => $this->amount->getCurrency()->getNumericCode(),
            'NUMAPPEL' => $this->numappel,
            'NUMTRANS' => $this->numtrans,
        ];
    }
}
