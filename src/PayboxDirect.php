<?php

namespace Paybox;

use GuzzleHttp\Client;

/**
 * Paybox Direct handles server-to-server communications.
 */
class PayboxDirect
{
    const PAYBOX_URL         = 'https://ppps.paybox.com/PPPS.php';
    const PAYBOX_BACKUP_URL  = 'https://ppps1.paybox.com/PPPS.php';
    const PAYBOX_PREPROD_URL = 'https://preprod-ppps.paybox.com/PPPS.php';

    const E_TRANSACTIONS_URL         = 'https://ppps.e-transactions.fr/PPPS.php';
    const E_TRANSACTIONS_PREPROD_URL = 'https://preprod-ppps.e-transactions.fr/PPPS.php';

    /**
     * @var Paybox
     */
    private $paybox;

    /**
     * @var string
     */
    private $url;

    /**
     * PayboxDirect constructor.
     *
     * @param Paybox $paybox
     * @param string $url
     */
    public function __construct(Paybox $paybox, $url)
    {
        $this->paybox = $paybox;
        $this->url    = $url;
    }

    /**
     * @param \Paybox\PayboxDirectRequest $request An instance of a Paybox request object.
     *
     * @return \Paybox\PayboxDirectResponse The Paybox response.
     *
     * @throws \GuzzleHttp\Exception\RequestException If the communication with the server fails.
     */
    public function execute(PayboxDirectRequest $request)
    {
        $values = $request->getValues();

        $values['SITE']        = $this->paybox->getSite();
        $values['RANG']        = $this->paybox->getRank();
        $values['VERSION']     = '00104';
        $values['DATEQ']       = gmdate('dmY'); // dmYHis
        $values['NUMQUESTION'] = $this->getNumQuestion();
        $values['HASH']        = 'SHA512';
        $values['HMAC']        = $this->paybox->hashHMAC($values);

        $httpClient = new Client();

        $response = $httpClient->request('POST', $this->url, [
            'form_params' => $values
        ]);

        $body = (string) $response->getBody();

        return new PayboxDirectResponse($body);
    }

    /**
     * Generates a pseudo-unique (across a 24-hour period) request number.
     *
     * Note that this does not 100% guarantee that the number will be unique: a conflict could happen
     * if two concurrent requests occur within the same 100 µs window, which is highly unlikely.
     *
     * Note that we cannot use 10 significant digits, as the documentation states that the the maximum value
     * for this number is 2147483647. So we always start with a 0, and use 9 significant digits instead.
     *
     * @return string
     */
    private function getNumQuestion()
    {
        list ($fraction, $timestamp) = explode(' ', microtime());

        $secondOfDay = sprintf('%05u', $timestamp % 86400);
        $fractionDigits = substr($fraction, 2, 4);

        return '0' . $secondOfDay . $fractionDigits;
    }
}
