<?php

namespace Paybox;

use Paybox\Utility\StringValidator;

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
     * @param string $number        The card number, 12 to 19 digits.
     * @param string $validityMonth The validity month, 1 to 2 digits.
     * @param string $validityYear  The validity year, 1 to 2 digits.
     * @param string $cvv           The CVV code, 3 to 4 digits.
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($number, $validityMonth, $validityYear, $cvv)
    {
        $number        = StringValidator::checkString('cardNumber', $number, 12, 19, true);
        $validityMonth = StringValidator::checkString('validityMonth', $validityMonth, 1, 2, true);
        $validityYear  = StringValidator::checkString('validityYear', $validityYear, 1, 2, true);
        $cvv           = StringValidator::checkString('cvv', $cvv, 3, 4, true);

        $validityMonthInt = (int) $validityMonth;

        if ($validityMonthInt < 1 || $validityMonthInt > 12) {
            throw new \InvalidArgumentException('Invalid card validity month.');
        }

        $validityMonth = str_pad($validityMonth, 2, '0', STR_PAD_LEFT);
        $validityYear  = str_pad($validityYear, 2, '0', STR_PAD_LEFT);

        $this->number   = $number;
        $this->validity = $validityMonth . $validityYear;
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
