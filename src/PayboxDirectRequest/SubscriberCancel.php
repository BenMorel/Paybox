<?php

namespace Paybox\PayboxDirectRequest;

use Paybox\PayboxDirectRequest;
use Brick\Money\Money;

/**
 * Cancel of a transaction on a subscriber.
 */
class SubscriberCancel implements PayboxDirectRequest
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
    private $token;

    /**
     * @var string
     */
    private $validity;

    /**
     * @var string
     */
    private $numappel;

    /**
     * @var string
     */
    private $numtrans;

    /**
     * SubscriberCancel constructor.
     *
     * @param Money  $amount              The captured amount of the transaction to cancel.
     * @param string $paymentReference    The merchant reference of the transaction to cancel.
     * @param string $subscriberReference The reference of the subscriber.
     *                                    This is the free field reference used when creating the subscriber.
     * @param string $token               The token returned in response to SubscriberRegister.
     * @param string $validity            The card validity date, in MMYY format.
     * @param string $numappel            The value returned in response to SubscriberCapture or SubscriberAuthorizeAndCapture.
     * @param string $numtrans            The value returned in response to SubscriberCapture or SubscriberAuthorizeAndCapture.
     */
    public function __construct(Money $amount, $paymentReference, $subscriberReference, $token, $validity, $numappel, $numtrans)
    {
        $this->amount              = $amount;
        $this->paymentReference    = $paymentReference;
        $this->subscriberReference = $subscriberReference;
        $this->token               = $token;
        $this->validity            = $validity;
        $this->numappel            = $numappel;
        $this->numtrans            = $numtrans;
    }

    /**
     * @inheritdoc
     */
    public function getValues()
    {
        return [
            'TYPE'      => '00055',
            'REFABONNE' => $this->subscriberReference,
            'REFERENCE' => $this->paymentReference,
            'MONTANT'   => $this->amount->getMinorAmount()->toInt(),
            'DEVISE'    => $this->amount->getCurrency()->getNumericCode(),
            'NUMAPPEL'  => $this->numappel,
            'NUMTRANS'  => $this->numtrans,
            'PORTEUR'   => $this->token,
            'DATEVAL'   => $this->validity,
        ];
    }
}
