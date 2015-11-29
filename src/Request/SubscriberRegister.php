<?php

namespace Paybox\Request;

use Paybox\Card;
use Paybox\Request;
use Brick\Money\Money;

/**
 * Register new subscriber.
 */
class SubscriberRegister implements Request
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
     * @param Card   $card      The payment card.
     * @param Money  $amount    The amount to authorize.
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
            'TYPE' => '00056',
            'REFABONNE' => $this->reference,
            'MONTANT'   => $this->amount->getAmount()->unscaledValue(),
            'DEVISE'    => $this->amount->getCurrency()->getNumericCode(),
            'PORTEUR'   => $this->card->getNumber(),
            'DATEVAL'   => $this->card->getValidity(),
            'CVV'       => $this->card->getCvv(),
        ];
    }
}
