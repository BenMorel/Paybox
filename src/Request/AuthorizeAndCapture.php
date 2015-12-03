<?php

namespace Paybox\Request;

use Paybox\Card;
use Paybox\Request;
use Brick\Money\Money;

/**
 * Authorization + Capture.
 */
class AuthorizeAndCapture implements Request
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
     * AuthorizeAndCapture constructor.
     *
     * @param Card   $card      The payment card.
     * @param Money  $amount    The amount to debit.
     * @param string $reference The merchant reference, free field from 1 to 250 characters.
     */
    public function __construct(Card $card, Money $amount, $reference)
    {
        $this->amount    = $amount;
        $this->card      = $card;
        $this->reference = $reference;
    }

    /**
     * Sets the authentication code from 3D Secure.
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
            'TYPE'      => '00003',
            'MONTANT'   => $this->amount->getAmount()->unscaledValue(),
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
