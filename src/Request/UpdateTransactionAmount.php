<?php

namespace Paybox\Request;

use Paybox\Request;
use Brick\Money\Money;

/**
 * Updates the amount of a transaction.
 */
class UpdateTransactionAmount implements Request
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
    private $numappel;

    /**
     * @var string
     */
    private $numtrans;

    /**
     * @var string
     */
    private $authorization;

    /**
     * UpdateTransactionAmount constructor.
     *
     * @param Money  $amount        The new transaction amount.
     * @param string $reference     The merchant reference of the original transaction.
     * @param string $numappel      The value returned by Capture or AuthorizeAndCapture.
     * @param string $numtrans      The value returned by Capture or AuthorizeAndCapture.
     * @param string $authorization The value returned by Capture or AuthorizeAndCapture.
     */
    public function __construct(Money $amount, $reference, $numappel, $numtrans, $authorization)
    {
        $this->amount        = $amount;
        $this->reference     = $reference;
        $this->numappel      = $numappel;
        $this->numtrans      = $numtrans;
        $this->authorization = $authorization;
    }

    /**
     * @inheritdoc
     */
    public function getValues()
    {
        return [
            'TYPE'         => '00013',
            'MONTANT'      => $this->amount->getAmount()->unscaledValue(),
            'DEVISE'       => $this->amount->getCurrency()->getNumericCode(),
            'REFERENCE'    => $this->reference,
            'NUMAPPEL'     => $this->numappel,
            'NUMTRANS'     => $this->numtrans,
            'AUTORISATION' => $this->authorization,
        ];
    }
}
