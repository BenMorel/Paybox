<?php

namespace Paybox;

use Brick\Money\Currency;
use Brick\Money\Money;

/**
 * A request for payment to send to Paybox System.
 *
 * This request will be sent from the customer's browser use an HTML form.
 */
class PayboxSystemRequest
{
    /**
     * @var array
     */
    private $values;

    /**
     * PayboxSystemRequest constructor.
     *
     * @param Money  $amount
     * @param string $reference
     * @param string $email
     */
    public function __construct(Money $amount, $reference, $email)
    {
        $this->values = [
            'PBX_TOTAL'   => $amount->getAmount()->unscaledValue(),
            'PBX_DEVISE'  => $amount->getCurrency()->getNumericCode(),
            'PBX_CMD'     => $reference,
            'PBX_PORTEUR' => $email,
            'PBX_RETOUR'  => 'M:M;R:R;T:T;A:A;B:B;C:C;D:D;E:E;F:F;G:G;H:H;I:I;J:J;N:N;O:O;P:P;Q:Q;S:S;U:U;W:W;Y:Y;K:K',
            'PBX_HASH'    => 'SHA512',
            'PBX_TIME'    => gmdate('c'),
        ];
    }

    /**
     * Sets a single return URL for all outcomes.
     *
     * - payment success
     * - payment failure
     * - payment cancelled
     * - payment pending
     *
     * @param string $returnUrl
     *
     * @return void
     */
    public function setReturnUrl($returnUrl)
    {
        $this->setSuccessUrl($returnUrl);
        $this->setFailureUrl($returnUrl);
        $this->setCancelUrl($returnUrl);
        $this->setPendingUrl($returnUrl);
    }

    /**
     * Sets the return URL if the payment is successful.
     *
     * If not set, the default URL from the merchant's account is used.
     *
     * @param string $successUrl
     *
     * @return void
     */
    public function setSuccessUrl($successUrl)
    {
        $this->values['PBX_EFFECTUE'] = $successUrl;
    }

    /**
     * Sets the return URL if the payment is refused.
     *
     * If not set, the default URL from the merchant's account is used.
     *
     * @param string $failureUrl
     *
     * @return void
     */
    public function setFailureUrl($failureUrl)
    {
        $this->values['PBX_REFUSE'] = $failureUrl;
    }

    /**
     * Sets the return URL if the payment is cancelled.
     *
     * If not set, the default URL from the merchant's account is used.
     *
     * @param string $cancelUrl
     *
     * @return void
     */
    public function setCancelUrl($cancelUrl)
    {
        $this->values['PBX_ANNULE'] = $cancelUrl;
    }

    /**
     * Sets the return URL if the payment is pending validation.
     *
     * If not set, the default URL from the merchant's account is used.
     *
     * @param string $pendingUrl
     *
     * @return void
     */
    public function setPendingUrl($pendingUrl)
    {
        $this->values['PBX_ATTENTE'] = $pendingUrl;
    }

    /**
     * Sets the callback (IPN) URL.
     *
     * If not set, the default URL from the merchant's account is used.
     *
     * @param string $callbackUrl
     *
     * @return void
     */
    public function setCallbackUrl($callbackUrl)
    {
        $this->values['PBX_REPONDRE_A'] = $callbackUrl;
    }

    /**
     * Sets the list of currencies to display on the payment page.
     *
     * This method accepts Currency instances or currency codes such as EUR, USD, etc.
     * The special code NO_CURR can be used to display no currency.
     *
     * @param Currency|string ...$currencies
     *
     * @return void
     */
    public function setCurrencies(... $currencies)
    {
        $codes = [];

        foreach ($currencies as $currency) {
            if ($currency instanceof Currency) {
                $codes[] = $currency->getCode();
            } else {
                $codes[] = $currency;
            }
        }

        $this->values['PBX_CURRENCYDISPLAY'] = $codes;
    }

    /**
     * Sets the minimum validity date for the card.
     *
     * If the card expiry is before the given month/year, the payment will be refused.
     * This is useful for payments in instalments, to prevent an instalment from failing because of the validity date.
     *
     * You can use the month of your last instalment to guarantee that the card will not be expired at this date.
     *
     * @param string $month The month, as 1 or 2 digits.
     * @param string $year  The year, as 2 or 4 digits.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function setMinCardValidity($month, $year)
    {
        if (preg_match('/^[0-9]{1,2}$/', $month) == 0) {
            throw new \InvalidArgumentException('Invalid month.');
        }

        if ($month < 1 || $month > 12) {
            throw new \InvalidArgumentException('Invalid month.');
        }

        if (strlen($month) == 1) {
            $month = '0' . $month;
        }

        if (preg_match('/^(?:20)?([0-9]{2})$/', $year, $matches) == 0) {
            throw new \InvalidArgumentException('Invalid year.');
        }

        $year = $matches[1];

        $this->values['PBX_DATEVALMAX'] = $year . $month;
    }

    /**
     * Sets the payment page timeout, in seconds.
     *
     * If the timeout is reached, the transaction is cancelled.
     *
     * @param int $timeout
     *
     * @return void
     */
    public function setPaymentPageTimeout($timeout)
    {
        $this->values['PBX_DISPLAY'] = (string) $timeout;
    }

    /**
     * Sets the card imprint returned by a previous response.
     *
     * @param string $imprint
     *
     * @return void
     */
    public function setCardImprint($imprint)
    {
        $this->values['PBX_EMPREINTE'] = $imprint;
    }

    /**
     * Perform an authorization only. The payment must be explicitly captured afterwards.
     *
     * @return void
     */
    public function authorizationOnly()
    {
        $this->values['PBX_AUTOSEULE'] = 'O';
    }

    /**
     * Do not perform the 3D Secure authentication for this transaction.
     *
     * @return void
     */
    public function bypass3DSecure()
    {
        $this->values['PBX_3DS'] = 'N';
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }
}
