<?php

namespace Paybox\Request;

use Paybox\Request;
use Brick\Money\Money;

/**
 * Credits a subscriber's card.
 */
class SubscriberCredit implements Request
{
    /**
     * @var Money
     */
    private $amount;

    /**
     * @var string
     */
    private $subscriberReference;

    /**
     * @var string
     */
    private $paymentReference;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $validity;

    /**
     * SubscriberCredit constructor.
     *
     * @param Money  $amount              The amount to credit on the subscriber's card.
     * @param string $subscriberReference The subscriber reference, as used in SubscriberRegister.
     * @param string $paymentReference    The merchant reference for this transaction, free field from 1 to 250 chars.
     * @param string $token               The token returned in response to SubscriberRegister.
     * @param string $validity            The card validity date, in MMYY format.
     */
    public function __construct(Money $amount, $subscriberReference, $paymentReference, $token, $validity)
    {
        $this->amount              = $amount;
        $this->subscriberReference = $subscriberReference;
        $this->paymentReference    = $paymentReference;
        $this->token               = $token;
        $this->validity            = $validity;
    }

    /**
     * @inheritdoc
     */
    public function getValues()
    {
        return [
            'TYPE'      => '00054',
            'MONTANT'   => $this->amount->getAmount()->unscaledValue(),
            'DEVISE'    => $this->amount->getCurrency()->getNumericCode(),
            'REFERENCE' => $this->paymentReference,
            'REFABONNE' => $this->subscriberReference,
            'PORTEUR'   => $this->token,
            'DATEVAL'   => $this->validity,
        ];
    }
}
