<?php

namespace Paybox\PayboxDirectRequest;

use Paybox\Card;
use Paybox\PayboxDirectRequest;
use Brick\Money\Money;

/**
 * Update an existing subscriber.
 */
class SubscriberUpdate implements PayboxDirectRequest
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
     * SubscriberUpdate constructor.
     *
     * @param Card   $card      The payment card.
     * @param Money  $amount    The amount to authorize. An authorization is made to check the card.
     * @param string $reference The reference of the subscriber to update.
     *                          This is the free field reference used when creating the subscriber.
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
            'TYPE'      => '00057',
            'REFABONNE' => $this->reference,
            'MONTANT'   => $this->amount->getMinorAmount()->toInt(),
            'DEVISE'    => $this->amount->getCurrency()->getNumericCode(),
            'PORTEUR'   => $this->card->getNumber(),
            'DATEVAL'   => $this->card->getValidity(),
            'CVV'       => $this->card->getCvv(),
        ];
    }
}
