<?php

namespace Paybox;

/**
 * A request to send to Paybox, such as Authorize or Capture.
 */
interface Request
{
    /**
     * @return array
     */
    public function getValues();
}
