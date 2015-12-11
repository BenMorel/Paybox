<?php

namespace Paybox\PayboxDirectRequest;

use Paybox\PayboxDirectRequest;
use Brick\Money\Money;

/**
 * Cancels a captured transaction.
 */
class Cancel implements PayboxDirectRequest
{
    /**
     * @var Money
     */
    private $amount;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var string
     */
    private $numappel;

    /**
     * @var string
     */
    private $numtrans;

    /**
     * Cancel constructor.
     *
     * @param Money  $amount    The captured amount of the transaction to cancel.
     * @param string $reference The merchant reference of the transaction to cancel.
     * @param string $numappel  The value returned in response to Capture or AuthorizeAndCapture.
     * @param string $numtrans  The value returned in response to Capture or AuthorizeAndCapture.
     */
    public function __construct(Money $amount, $reference, $numappel, $numtrans)
    {
        $this->amount    = $amount;
        $this->reference = $reference;
        $this->numappel  = $numappel;
        $this->numtrans  = $numtrans;
    }

    /**
     * @inheritdoc
     */
    public function getValues()
    {
        return [
            'TYPE'      => '00005',
            'MONTANT'   => $this->amount->getAmount()->unscaledValue(),
            'DEVISE'    => $this->amount->getCurrency()->getNumericCode(),
            'REFERENCE' => $this->reference,
            'NUMAPPEL'  => $this->numappel,
            'NUMTRANS'  => $this->numtrans,
        ];
    }
}
