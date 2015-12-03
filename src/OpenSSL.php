<?php

namespace Paybox;

/**
 * Generates and checks message signatures using OpenSSL.
 */
class OpenSSL
{
    /**
     * The Paybox public key file.
     *
     * Note that the public key is the same for E-transactions from the Crédit Agricole.
     *
     * @see http://www1.paybox.com/wp-content/uploads/2014/03/pubkey.pem
     */
    const PAYBOX_PUBLIC_KEY = __DIR__ . '/../pubkey.pem';

    /**
     * Checks the signature of the given message.
     *
     * It is assumed that the signature is the last parameter of the urlencoded string, whatever its name.
     *
     * Messages need to be decoded in a very picky way, due to the inconsistent URL-encoding of Paybox.
     * This is why this method accepts the raw query string (GET) or message body (POST),
     * and not an already decoded array of key-value pairs.
     *
     * @param string $message       The raw message to check.
     * @param bool   $isPost        True if the message comes from a POST request (return URL), false if it comes from a GET request (callback URL).
     * @param string $publicKeyFile The path to the public key file. Optional, defaults to Paybox's public key.
     *
     * @return bool True if the signature of the message is valid, false if it is invalid.
     *
     * @throws OpenSSLException If the certificate file is invalid, or an OpenSSL error occurs.
     */
    public function checkSignature($message, $isPost, $publicKeyFile = OpenSSL::PAYBOX_PUBLIC_KEY)
    {
        // Dequeue errors than would have been ignored by other libraries.
        // These errors are persistent across HTTP calls, and could add confusion to our error messages.
        $this->handleErrors();

        $publicKey = openssl_pkey_get_public('file://' . $publicKeyFile);

        $this->handleErrors($publicKey === false);

        $data = $this->parseMessage($message, $isPost);

        if (! $data) {
            return false;
        }

        $signature    = end($data);
        $signatureKey = key($data);

        unset($data[$signatureKey]);

        $signedMessage = [];

        foreach ($data as $key => $value) {
            $signedMessage[] = $key . '=' . $value;
        }

        $signedMessage = implode('&', $signedMessage);

        if ($isPost) {
            // The data is double-URL-encoded in this case.
            $signature = rawurldecode($signature);
        }

        $signature = base64_decode($signature);

        $result = openssl_verify($signedMessage, $signature, $publicKey);

        $this->handleErrors($result == -1);

        return (bool) $result;
    }

    /**
     * @param string $message
     * @param bool   $isPost
     *
     * @return array
     */
    private function parseMessage($message, $isPost)
    {
        $pairs = explode('&', $message);

        $data = [];

        foreach ($pairs as $pair) {
            $pos = strpos($pair, '=');

            if ($pos === false) {
                $data[$pair] = '';

                continue;
            }

            $key = substr($pair, 0, $pos);
            $value = substr($pair, $pos + 1);

            if ($isPost) {
                $data[$key] = urldecode($value);
            } else {
                $data[$key] = rawurldecode($value);
            }
        }

        return $data;
    }

    /**
     * Handles (dequeues) OpenSSL errors and warnings.
     *
     * @param bool $throw Whether to throw an exception with the queued error messages.
     *
     * @return void
     *
     * @throws OpenSSLException
     */
    private function handleErrors($throw = false)
    {
        $errors = [];

        for (;;) {
            $error = openssl_error_string();

            if ($error === false) {
                break;
            }

            $errors[] = $error;
        }

        if (! $throw) {
            return;
        }

        $message = implode(PHP_EOL, $errors);

        throw new OpenSSLException($message);
    }
}
