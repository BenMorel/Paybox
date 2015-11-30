<?php

namespace Paybox\Tests;

use Paybox\Card;
use Paybox\Paybox;
use Paybox\Request\Authorize;
use Paybox\Response;
use Brick\Money\Money;

/**
 * Tests for the Paybox class.
 *
 * @see http://www1.paybox.com/espace-integrateur-documentation/comptes-de-tests/
 * @see http://www1.paybox.com/espace-integrateur-documentation/cartes-de-tests/
 */
class PayboxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return Paybox
     */
    private function getTestPayboxInstance()
    {
        return new Paybox(
            '1999888',
            '63',
            '109518543',
            '0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF',
            Paybox::PAYBOX_PREPROD_URL
        );
    }

    /**
     * @param Response $response       The response to check.
     * @param string   $expectedStatus The expected Response::* status constant.
     */
    private function assertResponseStatus(Response $response, $expectedStatus)
    {
        $message = sprintf('Expected response status %s, got %s', $expectedStatus, $response->getCodereponse());
        $this->assertTrue($response->is($expectedStatus), $message);
    }

    /**
     * @dataProvider providerAuthorize
     *
     * @param string $cardNumber
     * @param string $validity
     * @param string $cvv
     * @param string $amount
     * @param string $expectedStatus
     */
    public function testAuthorize($cardNumber, $validity, $cvv, $amount, $expectedStatus)
    {
        $card = new Card($cardNumber, $validity, $cvv);
        $amount = Money::parse($amount);
        $reference = 'Paybox-Test-' . time();

        $paybox = $this->getTestPayboxInstance();

        $request = new Authorize($card, $amount, $reference);
        $response = $paybox->execute($request);

        $this->assertResponseStatus($response, $expectedStatus);
    }

    /**
     * @return array
     */
    public function providerAuthorize()
    {
        return [
            ['1111222233334444', '0216', '123', 'EUR 10', Response::SUCCESS],
            ['1111222233334444', '0101', '123', 'EUR 10', Response::INVALID_EXPIRY_DATE],
            ['1111222233335555', '1299', '123', 'EUR 10', Response::INVALID_CARD_NUMBER],
        ];
    }
}
