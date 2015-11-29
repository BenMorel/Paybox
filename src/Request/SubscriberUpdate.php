<?php

namespace Paybox\Request;

use Paybox\Card;
use Paybox\Request;
use Brick\Money\Money;

/**
 * Update an existing subscriber.
 */
class SubscriberUpdate implements Request
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
     * @param Money  $amount    The amount to authorize. @todo not sure yet why an amount is required.
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
            'MONTANT'   => $this->amount->getAmount()->unscaledValue(),
            'DEVISE'    => $this->amount->getCurrency()->getNumericCode(),
            'PORTEUR'   => $this->card->getNumber(),
            'DATEVAL'   => $this->card->getValidity(),
            'CVV'       => $this->card->getCvv(),
        ];
    }
}
