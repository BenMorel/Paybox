<?php

namespace Paybox;

use GuzzleHttp\Client;

/**
 * Helper class to communicate with Paybox servers.
 */
class Paybox
{
    const PAYBOX_URL           = 'https://ppps.paybox.com/PPPS.php';
    const PAYBOX_SECONDARY_URL = 'https://ppps1.paybox.com/PPPS.php';
    const PAYBOX_PREPROD_URL   = 'https://preprod-ppps.paybox.com/PPPS.php';

    const E_TRANSACTIONS_URL         = 'https://ppps.e-transactions.fr/PPPS.php';
    const E_TRANSACTIONS_PREPROD_URL = 'https://preprod-ppps.e-transactions.fr/PPPS.php';

    /**
     * The site number provided by Paybox.
     *
     * @var string
     */
    private $site;

    /**
     * The rank number provided by Paybox.
     *
     * @var string
     */
    private $rank;

    /**
     * The internal identifier provided by Paybox.
     *
     * @var string
     */
    private $identifier;

    /**
     * The secret HMAC authentication key, in binary format.
     *
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $url;

    /**
     * Paybox constructor.
     *
     * @param string $site       The site number provided by Paybox.
     * @param string $rank       The rank number provided by Paybox.
     * @param string $identifier The internal identifier provided by Paybox.
     * @param string $key        The secret HMAC authentication key, in hexadecimal format.
     * @param string $url        The URL to post requests to.
     */
    public function __construct($site, $rank, $identifier, $key, $url)
    {
        $this->site       = $site;
        $this->rank       = $rank;
        $this->identifier = $identifier;
        $this->key        = hex2bin($key);
        $this->url        = $url;
    }

    /**
     * Computes the HMAC hash of an array of key-value pairs.
     *
     * @param array $values An array of key-value pairs.
     *
     * @return string
     */
    private function hashHMAC(array $values)
    {
        $message = [];

        foreach ($values as $key => $value) {
            $message[] = $key . '=' . $value;
        }

        $message = implode('&', $message);

        return strtoupper(hash_hmac('sha512', $message, $this->key));
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

    /**
     * @param \Paybox\Request $request An instance of a Paybox request object.
     *
     * @return \Paybox\Response The Paybox response.
     *
     * @throws \Guzzle\Http\Exception\RequestException If the communication with the server fails.
     */
    public function execute(Request $request)
    {
        $values = $request->getValues();

        $values['SITE']        = $this->site;
        $values['RANG']        = $this->rank;
        $values['VERSION']     = '00104';
        $values['DATEQ']       = gmdate('dmY'); // dmYHis
        $values['NUMQUESTION'] = $this->getNumQuestion();
        $values['HASH']        = 'SHA512';

        $hmac = $this->hashHMAC($values);

        $values['HMAC'] = $hmac;

        $httpClient = new Client();

        $response = $httpClient->request('POST', $this->url, [
            'form_params' => $values
        ]);

        $body = (string) $response->getBody();
        parse_str($body, $data);

        return new Response($data);
    }
}
