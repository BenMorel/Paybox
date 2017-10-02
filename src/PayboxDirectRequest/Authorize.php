<?php

namespace Paybox\PayboxDirectRequest;

use Paybox\Card;
use Paybox\PayboxDirectRequest;
use Brick\Money\Money;

/**
 * Authorization only.
 */
class Authorize implements PayboxDirectRequest
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
     * The authentication code from 3D Secure, if set.
     *
     * @var string|null
     */
    private $id3d;

    /**
     * Authorize constructor.
     *
     * @param Card        $card      The payment card.
     * @param Money       $amount    The amount to authorize.
     * @param string      $reference The merchant reference, free field from 1 to 250 characters.
     */
    public function __construct(Card $card, Money $amount, $reference)
    {
        $this->amount    = $amount;
        $this->card      = $card;
        $this->reference = $reference;
    }

    /**
     * Sets c
     *
     * @param string $id3d
     *
     * @return void
     */
    public function setID3D($id3d)
    {
        $this->id3d = $id3d;
    }

    /**
     * @inheritdoc
     */
    public function getValues()
    {
        $values = [
            'TYPE'      => '00001',
            'MONTANT'   => $this->amount->getMinorAmount()->toInt(),
            'DEVISE'    => $this->amount->getCurrency()->getNumericCode(),
            'REFERENCE' => $this->reference,
            'PORTEUR'   => $this->card->getNumber(),
            'DATEVAL'   => $this->card->getValidity(),
            'CVV'       => $this->card->getCvv(),

            // optional
//            'AUTORISATION' => '',
        ];

        if ($this->id3d !== null) {
            $values['ID3D'] = $this->id3d;
        }

        return $values;
    }
}
