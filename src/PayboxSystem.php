<?php

namespace Paybox;

/**
 * Paybox System creates forms to redirect the user to payment pages hosted by Paybox.
 */
class PayboxSystem
{
    const PAYBOX_URL         = 'https://tpeweb.paybox.com/cgi/MYchoix_pagepaiement.cgi';
    const PAYBOX_BACKUP_URL  = 'https://tpeweb1.paybox.com/cgi/MYchoix_pagepaiement.cgi';
    const PAYBOX_PREPROD_URL = 'https://preprod-tpeweb.paybox.com/cgi/MYchoix_pagepaiement.cgi';

    const PAYBOX_IFRAME_URL         = 'https://tpeweb.paybox.com/cgi/MYframepagepaiement_ip.cgi';
    const PAYBOX_IFRAME_BACKUP_URL  = 'https://tpeweb1.paybox.com/cgi/MYframepagepaiement_ip.cgi';
    const PAYBOX_IFRAME_PREPROD_URL = 'https://preprod-tpeweb.paybox.com/cgi/MYframepagepaiement_ip.cgi';

    const PAYBOX_MOBILE_URL         = 'https://tpeweb.paybox.com/cgi/ChoixPaiementMobile.cgi';
    const PAYBOX_MOBILE_BACKUP_URL  = 'https://tpeweb1.paybox.com/cgi/ChoixPaiementMobile.cgi';
    const PAYBOX_MOBILE_PREPROD_URL = 'https://preprod-tpeweb.paybox.com/cgi/ChoixPaiementMobile.cgi';

    const E_TRANSACTIONS_URL         = 'https://tpeweb.e-transactions.fr/cgi/MYchoix_pagepaiement.cgi';
    const E_TRANSACTIONS_PREPROD_URL = 'https://preprod-tpeweb.e-transactions.fr/cgi/MYchoix_pagepaiement.cgi';

    const E_TRANSACTIONS_IFRAME_URL         = 'https://tpeweb.e-transactions.fr/cgi/MYframepagepaiement_ip.cgi';
    const E_TRANSACTIONS_IFRAME_PREPROD_URL = 'https://preprod-tpeweb.e-transactions.fr/cgi/MYframepagepaiement_ip.cgi';

    const E_TRANSACTIONS_MOBILE_URL         = 'https://tpeweb.e-transactions.fr/cgi/ChoixPaiementMobile.cgi';
    const E_TRANSACTIONS_MOBILE_PREPROD_URL = 'https://preprod-tpeweb.e-transactions.fr/cgi/ChoixPaiementMobile.cgi';

    /**
     * @var Paybox
     */
    private $paybox;

    /**
     * @var string
     */
    private $url;

    /**
     * PayboxSystem constructor.
     *
     * @param Paybox $paybox
     * @param string $url
     */
    public function __construct(Paybox $paybox, $url)
    {
        $this->paybox = $paybox;
        $this->url    = $url;
    }

    /**
     * @param PayboxSystemRequest $request
     *
     * @return array
     */
    public function getPostParameters(PayboxSystemRequest $request)
    {
        $values = $request->getValues();

        $values['PBX_SITE']        = $this->paybox->getSite();
        $values['PBX_RANG']        = $this->paybox->getRank();
        $values['PBX_IDENTIFIANT'] = $this->paybox->getIdentifier();
        $values['PBX_HMAC']        = $this->paybox->hashHMAC($values);

        return $values;
    }
}
