<?php

namespace Paybox;

/**
 * A request to send to Paybox, such as Authorize or Capture.
 */
interface PayboxDirectRequest
{
    /**
     * @return array
     */
    public function getValues();
}
