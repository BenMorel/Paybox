<?php

namespace Paybox\Request;

use Paybox\Request;
use Brick\Money\Money;

/**
 * Debit (Capture) on a subscriber.
 */
class SubscriberCapture implements Request
{
    /**
     * @var Money
     */
    private $amount;

    /**
     * @var string
     */
    private $paymentReference;

    /**
     * @var string
     */
    private $subscriberReference;

    /**
     * @var string
     */
    private $numappel;

    /**
     * @var string
     */
    private $numtrans;

    /**
     * Capture constructor.
     *
     * @param Money  $amount              The amount to capture. Must be lower than or equal to the authorized amount.
     * @param string $paymentReference    The merchant reference of the transaction to capture.
     * @param string $subscriberReference The reference of the subscriber.
     *                                    This is the free field reference used when creating the subscriber.
     * @param string $numappel            The value returned in response to SubscriberAuthorization.
     * @param string $numtrans            The value returned in response to SubscriberAuthorization.
     */
    public function __construct(Money $amount, $paymentReference, $subscriberReference, $numappel, $numtrans)
    {
        $this->amount              = $amount;
        $this->paymentReference    = $paymentReference;
        $this->subscriberReference = $subscriberReference;
        $this->numappel            = $numappel;
        $this->numtrans            = $numtrans;
    }

    /**
     * @inheritdoc
     */
    public function getValues()
    {
        return [
            'TYPE'      => '00052',
            'MONTANT'   => $this->amount->getAmount()->unscaledValue(),
            'DEVISE'    => $this->amount->getCurrency()->getNumericCode(),
            'NUMAPPEL'  => $this->numappel,
            'NUMTRANS'  => $this->numtrans,
            'REFERENCE' => $this->paymentReference,
            'REFABONNE' => $this->subscriberReference,
        ];
    }
}
