<?php

namespace Paybox;

/**
 * Configuration class for Paybox.
 */
class Paybox
{
    /**
     * The site number.
     *
     * @var string
     */
    private $site;

    /**
     * The rank number.
     *
     * @var string
     */
    private $rank;

    /**
     * The merchant identifier.
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
     * Paybox constructor.
     *
     * @param string $site       The site number.
     * @param string $rank       The rank number.
     * @param string $identifier The merchant identifier.
     * @param string $key        The secret HMAC authentication key, in hexadecimal format.
     */
    public function __construct($site, $rank, $identifier, $key)
    {
        $this->site       = $site;
        $this->rank       = $rank;
        $this->identifier = $identifier;
        $this->key        = hex2bin($key);
    }

    /**
     * @return string
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @return string
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Computes the HMAC hash of an array of key-value pairs.
     *
     * @internal
     *
     * @param array $values An array of key-value pairs.
     *
     * @return string
     */
    public function hashHMAC(array $values)
    {
        $message = [];

        foreach ($values as $key => $value) {
            $message[] = $key . '=' . $value;
        }

        $message = implode('&', $message);

        return strtoupper(hash_hmac('sha512', $message, $this->key));
    }
}
