<?php

namespace Paybox\PayboxDirectRequest;

use Paybox\Card;
use Paybox\PayboxDirectRequest;
use Brick\Money\Money;

/**
 * Register new subscriber.
 */
class SubscriberRegister implements PayboxDirectRequest
{
    /**
     * @var Card
     */
    private $card;

    /**
     * @var Money
     */
    private $amount;

    /**
     * @var string
     */
    private $reference;

    /**
     * SubscriberRegister constructor.
     *
     * @param Card   $card      The payment card.
     * @param Money  $amount    The amount to authorize. An authorization is made to check the card.
     * @param string $reference The subscriber reference, free field from 1 to 250 characters.
     *                          Must be unique across all subscribers.
     */
    public function __construct(Card $card, Money $amount, $reference)
    {
        $this->amount    = $amount;
        $this->card      = $card;
        $this->reference = $reference;
    }

    /**
     * @inheritdoc
     */
    public function getValues()
    {
        return [
            'TYPE'      => '00056',
            'REFABONNE' => $this->reference,
            'MONTANT'   => $this->amount->getMinorAmount()->toInt(),
            'DEVISE'    => $this->amount->getCurrency()->getNumericCode(),
            'PORTEUR'   => $this->card->getNumber(),
            'DATEVAL'   => $this->card->getValidity(),
            'CVV'       => $this->card->getCvv(),
        ];
    }
}
