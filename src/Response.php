<?php

namespace Paybox;

/**
 * A response from Paybox.
 */
class Response
{
    /**
     * Successful operation.
     */
    const SUCCESS = '00000';

    /**
     * Connection to the authorization center failed or an internal error occurred.
     *
     * In this case, it is advised to try on the backup site.
     */
    const CONNECTION_FAILED = '00001';

    /**
     * Payment rejected by the authorization center.
     */
    const PAYMENT_REFUSED   = '001..';

    /**
     * A coherence error occurred.
     */
    const COHERENCE_ERROR = '00002';

    /**
     * Paybox error.
     *
     * In this case, it is advised to try on the backup site.
     */
    const PAYBOX_ERROR = '00003';

    /**
     * Invalid card number.
     *
     * Note that this should not happen, as question numbers are handled by the library.
     */
    const INVALID_CARD_NUMBER = '00004';

    /**
     * Invalid question number.
     */
    const INVALID_QUESTION_NUMBER = '00005';

    /**
     * Access refused or site/rank/identifier incorrect.
     */
    const ACCESS_REFUSED = '00006';

    /**
     * Invalid date.
     *
     * Note that this should not happen, as dates are handled by the library.
     */
    const INVALID_DATE = '00007';

    /**
     * Invalid expiry date.
     *
     * Note: don't rely solely on this error: it is not always used when the expiry date is invalid.
     * You might also receive a PAYMENT_REFUSED code in that particular case.
     */
    const INVALID_EXPIRY_DATE = '00008';

    /**
     * Error during subscriber creation.
     *
     * @todo French version says "Type d’opération invalide.", which contradicts English version.
     */
    const SUBSCRIBER_CREATION_ERROR = '00009';

    /**
     * Unknown currency.
     */
    const UNKNOWN_CURRENCY = '00010';

    /**
     * Incorrect amount.
     */
    const INCORRECT_AMOUNT = '00011';

    /**
     * Invalid order reference.
     */
    const INVALID_REFERENCE = '00012';

    /**
     * The requested version is not supported anymore.
     */
    const UNSUPPORTED_VERSION = '00013';

    /**
     * Incoherent frame received.
     */
    const INCOHERENT_FRAME = '00014';

    /**
     * Payment already done.
     */
    const PAYMENT_ALREADY_DONE = '00015';

    /**
     * Subscriber already exists (in response to SubscriberRegister).
     */
    const SUBSCRIBER_ALREADY_EXISTS = '00016';

    /**
     * Subscriber does not exist.
     */
    const SUBSCRIBER_DOES_NOT_EXIST = '00017';

    /**
     * Transaction not found (in response to CheckTransactionExistence).
     */
    const TRANSACTION_NOT_FOUND = '00018';

    /**
     * CVV not present.
     */
    const CVV_NOT_PRESENT = '00020';

    /**
     * Unauthorized bin card.
     */
    const UNAUTHORIZED_BIN_CARD = '00021';

    /**
     * Payment limit reached.
     */
    const PAYMENT_LIMIT_REACHED = '00022';

    /**
     * Country code filtered for this merchant.
     */
    const COUNTRY_FILTERED = '00024';

    /**
     * A request with the same question number has already been processed.
     */
    const DUPLICATE_REQUEST = '00039';

    /**
     * @var string
     */
    private $numtrans = '';

    /**
     * @var string
     */
    private $numappel = '';

    /**
     * @var string
     */
    private $numquestion = '';

    /**
     * @var string
     */
    private $site = '';

    /**
     * @var string
     */
    private $rang = '';

    /**
     * @var string
     */
    private $autorisation = '';

    /**
     * @var string
     */
    private $codereponse = '';

    /**
     * @var string
     */
    private $commentaire = '';

    /**
     * @var string
     */
    private $subscriberReference = '';

    /**
     * @var string
     */
    private $token = '';

    /**
     * Response constructor.
     *
     * @param array $data An associative array of data as returned by the Paybox server.
     */
    public function __construct(array $data)
    {
        $map = [
            'NUMTRANS'     => 'numtrans',
            'NUMAPPEL'     => 'numappel',
            'NUMQUESTION'  => 'numquestion',
            'SITE'         => 'site',
            'RANG'         => 'rang',
            'AUTORISATION' => 'autorisation',
            'CODEREPONSE'  => 'codereponse',
            'COMMENTAIRE'  => 'commentaire',
            'REFABONNE'    => 'subscriberReference',
            'PORTEUR'      => 'token'
        ];

        foreach ($map as $key => $field) {
            if (isset($data[$key])) {
                $this->{$field} = $data[$key];
            }
        }
    }

    /**
     * Returns whether this response matches the given status.
     *
     * @param string $status One of the Response::* constants.
     *
     * @return bool
     */
    public function is($status)
    {
        return preg_match('/^' . $status . '$/', $this->codereponse) == 1;
    }

    /**
     * @return string
     */
    public function getNumtrans()
    {
        return $this->numtrans;
    }

    /**
     * @return string
     */
    public function getNumappel()
    {
        return $this->numappel;
    }

    /**
     * @return string
     */
    public function getNumquestion()
    {
        return $this->numquestion;
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
    public function getRang()
    {
        return $this->rang;
    }

    /**
     * @return string
     */
    public function getAutorisation()
    {
        return $this->autorisation;
    }

    /**
     * @return string
     */
    public function getCodereponse()
    {
        return $this->codereponse;
    }

    /**
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Returns the subscriber reference.
     *
     * Empty for non subscription related responses.
     *
     * @return string
     */
    public function getSubscriberReference()
    {
        return $this->subscriberReference;
    }

    /**
     * Returns the subscription token.
     *
     * Only set when registering or updating a subscription, empty otherwise.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
}
