<?php

namespace Paybox;

/**
 * A payment card.
 */
class Card
{
    /**
     * The card number.
     *
     * @var string
     */
    private $number;

    /**
     * The validity, in MMYY format.
     *
     * @var string
     */
    private $validity;

    /**
     * The CVV code.
     *
     * @var string
     */
    private $cvv;

    /**
     * Card constructor.
     *
     * @param string $number   The card number, 12 to 19 digits.
     * @param string $validity The validity month/year, 4 digits in MMYY format.
     * @param string $cvv      The CVV code, 3 to 4 digits.
     */
    public function __construct($number, $validity, $cvv)
    {
        $this->number   = $number;
        $this->validity = $validity;
        $this->cvv      = $cvv;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return string
     */
    public function getValidity()
    {
        return $this->validity;
    }

    /**
     * @return string
     */
    public function getCvv()
    {
        return $this->cvv;
    }
}
