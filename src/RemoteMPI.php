<?php

namespace Paybox;

use Brick\Money\Money;

/**
 * RemoteMPI (merchant plug-in) is Paybox implementation of 3D Secure.
 */
class RemoteMPI
{
    const PAYBOX_URL =         'https://tpeweb.paybox.com/cgi/RemoteMPI.cgi';
    const PAYBOX_BACKUP_URL  = 'https://tpeweb1.paybox.com/cgi/RemoteMPI.cgi';
    const PAYBOX_PREPROD_URL = 'https://preprod-tpeweb.paybox.com/cgi/RemoteMPI.cgi';

    const E_TRANSACTIONS_URL         = 'https://tpeweb.e-transactions.fr/cgi/RemoteMPI.cgi';
    const E_TRANSACTIONS_PREPROD_URL = 'https://preprod-tpeweb.e-transactions.fr/cgi/RemoteMPI.cgi';

    /**
     * @var Paybox
     */
    private $paybox;

    /**
     * @var string|null
     */
    private $callbackUrl;

    /**
     * @var string|null
     */
    private $returnUrl;

    /**
     * RemoteMPI constructor.
     *
     * @param Paybox $paybox
     */
    public function __construct(Paybox $paybox)
    {
        $this->paybox = $paybox;
    }

    /**
     * Sets a custom server-to-server callback URL.
     *
     * If not set, the account's default URL is used.
     *
     * @param string $url
     *
     * @return void
     */
    public function setCallbackUrl($url)
    {
        $this->callbackUrl = $url;
    }

    /**
     * Sets a custom return URL.
     *
     * The user will be returned to this URL after the authentication, whether it's successfull or not.
     * If not set, the account's default URL is used.
     *
     * @param string $url
     *
     * @return void
     */
    public function setReturnUrl($url)
    {
        $this->returnUrl = $url;
    }

    /**
     * Returns an array of POST parameters to use to redirect the customer to the 3D Secure authentication page.
     *
     * These parameters can be used to build a web form to post in the user's browser.
     *
     * @param Card  $card       The payment card.
     * @param Money $amount     The amount of the transaction.
     * @param string $sessionId A unique session ID of up to 250 chars.
     *                          This session ID will be returned in the callback,
     *                          and used to identify this 3D Secure authentication.
     *
     * @return array
     */
    public function getPostParameters(Card $card, Money $amount, $sessionId)
    {
        $parameters = [
            'Amount'     => $amount->getMinorAmount()->toInt(),
            'Currency'   => $amount->getCurrency()->getNumericCode(),

            'CCNumber'   => $card->getNumber(),
            'CCExpDate'  => $card->getValidity(),
            'CVVCode'    => $card->getCvv(),

            'IdMerchant' => $this->paybox->getIdentifier(),
            'IdSession'  => $sessionId
        ];

        if ($this->callbackUrl !== null) {
            $parameters['URLHttpDirect'] = $this->callbackUrl;
        }

        if ($this->returnUrl !== null) {
            $parameters['URLRetour'] = $this->returnUrl;
        }

        return $parameters;
    }

    /**
     * Checks the parameters received in the callback or return URL.
     *
     * Callback URL call (server-to-server) is a GET request.
     * Return URL call (user's browser) is a POST request.
     *
     * @param string $message The raw query or POST string.
     * @param bool   $isPost  Whether the message comes from a POST (true) or GET (false) request.
     *
     * @return RemoteMPIResponse
     *
     * @throws RemoteMPIException If the signature of the message is invalid.
     * @throws OpenSSLException   If an OpenSSL error occurs.
     */
    public function getResponse($message, $isPost)
    {
        $openSSL = new OpenSSL();

        if (! $openSSL->checkSignature($message, $isPost)) {
            throw new RemoteMPIException('Invalid message signature.');
        }

        parse_str($message, $data);

        return new RemoteMPIResponse($data);
    }
}
