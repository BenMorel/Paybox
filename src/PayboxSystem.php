<?php

namespace Paybox;

use Brick\Money\Money;

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
     * @param Money $amount     The amount to pay.
     * @param string $reference The merchant's order reference.
     * @param string $email     The customer's email address.
     *
     * @return array
     */
    public function getPostParameters(Money $amount, $reference, $email)
    {
        $values = [
            'PBX_SITE' => $this->paybox->getSite(),
            'PBX_RANG' => $this->paybox->getRank(),
            'PBX_IDENTIFIANT' => $this->paybox->getIdentifier(),
            'PBX_TOTAL' => $amount->getAmount()->unscaledValue(),
            'PBX_DEVISE' => $amount->getCurrency()->getNumericCode(),
            'PBX_CMD' => $reference,
            'PBX_PORTEUR' => $email,
            'PBX_RETOUR' => 'Mt:M;Ref:R;Auto:A;Erreur:E',
            'PBX_HASH' => 'SHA512',
            'PBX_TIME' => gmdate('c'),
        ];

        $values['PBX_HMAC'] = $this->paybox->hashHMAC($values);

        return $values;
    }
}
