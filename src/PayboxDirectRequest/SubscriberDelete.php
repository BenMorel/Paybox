<?php

namespace Paybox\PayboxDirectRequest;

use Paybox\PayboxDirectRequest;

/**
 * Delete a subscriber.
 */
class SubscriberDelete implements PayboxDirectRequest
{
    /**
     * @var string
     */
    private $reference;

    /**
     * SubscriberDelete constructor.
     *
     * @param string $reference The reference of the subscriber to delete.
     *                          This is the free field reference used when creating the subscriber.
     */
    public function __construct($reference)
    {
        $this->reference = $reference;
    }

    /**
     * @inheritdoc
     */
    public function getValues()
    {
        return [
            'TYPE'      => '00058',
            'REFABONNE' => $this->reference,
        ];
    }
}
