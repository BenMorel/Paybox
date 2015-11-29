<?php

namespace Paybox\Request;

use Paybox\Request;

/**
 * Delete a subscriber.
 */
class SubscriberDelete implements Request
{
    /**
     * @var string
     */
    private $reference;

    /**
     * SubscriberDelete constructor.
     *
     * @param string $reference The subscriber reference.
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
