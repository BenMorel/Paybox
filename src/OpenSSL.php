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
     * @param string $message       The raw urlencoded message to check.
     * @param string $publicKeyFile The path to the public key file. Optional, defaults to Paybox's public key.
     *
     * @return bool True if the signature of the message is valid, false if it is invalid.
     *
     * @throws OpenSSLException If the certificate file is invalid, or an OpenSSL error occurs.
     */
    public function checkSignature($message, $publicKeyFile = OpenSSL::PAYBOX_PUBLIC_KEY)
    {
        // Dequeue errors than would have been ignored by other libraries.
        // These errors are persistent across HTTP calls, and could add confusion to our error messages.
        $this->handleErrors();

        $publicKey = openssl_pkey_get_public('file://' . $publicKeyFile);

        $this->handleErrors($publicKey === false);

        $lastAmpPos = strrpos($message, '&');

        if ($lastAmpPos === false) {
            return false;
        }

        $signedData = substr($message, 0, $lastAmpPos);

        $equalsPos = strpos($message, '=', $lastAmpPos);

        if ($equalsPos === false) {
            return false;
        }

        $signature = substr($message, $equalsPos + 1);
        $signature = urldecode($signature);
        $signature = base64_decode($signature);

        $result = openssl_verify($signedData, $signature, $publicKey);

        $this->handleErrors($result == -1);

        return (bool) $result;
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
