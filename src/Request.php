<?php

namespace Paybox;

/**
 * A request to send to Paybox, such as Authorize, Capture, Void, etc.
 */
interface Request
{
    /**
     * @return array
     */
    public function getValues();
}
