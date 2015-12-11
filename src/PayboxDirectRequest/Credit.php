<?php

namespace Paybox\PayboxDirectRequest;

use Paybox\Card;
use Paybox\PayboxDirectRequest;
use Brick\Money\Money;

/**
 * Credit.
 */
class Credit implements PayboxDirectRequest
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
     * Credit constructor.
     *
     * @param Card   $card
     * @param Money  $amount
     * @param string $reference
     */
    public function __construct(Card $card, Money $amount, $reference)
    {
        $this->card      = $card;
        $this->amount    = $amount;
        $this->reference = $reference;
    }

    /**
     * @inheritdoc
     */
    public function getValues()
    {
        return [
            'TYPE'      => '00004',
            'MONTANT'   => $this->amount->getAmount()->unscaledValue(),
            'DEVISE'    => $this->amount->getCurrency()->getNumericCode(),
            'REFERENCE' => $this->reference,
            'PORTEUR'   => $this->card->getNumber(),
            'DATEVAL'   => $this->card->getValidity(),
            'CVV'       => $this->card->getCvv(),
        ];
    }
}
