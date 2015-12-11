<?php

namespace Paybox\PayboxDirectRequest;

use Paybox\PayboxDirectRequest;
use Brick\Money\Money;

/**
 * Transaction without authorization request on a subscriber.
 */
class SubscriberTransactionWithoutAuthorization implements PayboxDirectRequest
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
    private $refabonne;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $validity;

    /**
     * SubscriberTransactionWithoutAuthorization constructor.
     *
     * @param Money  $amount    The amount to debit.
     * @param string $reference The merchant reference, free field from 1 to 250 characters.
     * @param string $refabonne The subscriber reference.
     * @param string $token     The token returned in response to SubscriberRegister.
     * @param string $validity  The card validity date, in MMYY format.
     */
    public function __construct(Money $amount, $reference, $refabonne, $token, $validity)
    {
        $this->amount    = $amount;
        $this->reference = $reference;
        $this->refabonne = $refabonne;
        $this->token     = $token;
        $this->validity  = $validity;
    }

    /**
     * @inheritdoc
     */
    public function getValues()
    {
        return [
            'TYPE'      => '00061',
            'MONTANT'   => $this->amount->getAmount()->unscaledValue(),
            'DEVISE'    => $this->amount->getCurrency()->getNumericCode(),
            'REFERENCE' => $this->reference,
            'REFABONNE' => $this->refabonne,
            'PORTEUR'   => $this->token,
            'DATEVAL'   => $this->validity,
        ];
    }
}
