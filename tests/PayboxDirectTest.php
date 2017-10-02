<?php

namespace Paybox\Tests;

use Paybox\Card;
use Paybox\Paybox;
use Paybox\PayboxDirect;
use Paybox\PayboxDirectRequest\Authorize;
use Paybox\PayboxDirectRequest\AuthorizeAndCapture;
use Paybox\PayboxDirectRequest\Cancel;
use Paybox\PayboxDirectRequest\Capture;
use Paybox\PayboxDirectRequest\CheckTransactionExistence;
use Paybox\PayboxDirectRequest\Credit;
use Paybox\PayboxDirectRequest\Inquire;
use Paybox\PayboxDirectResponse;

use Brick\Money\Money;

/**
 * Live tests for Paybox Direct.
 *
 * @see http://www1.paybox.com/espace-integrateur-documentation/comptes-de-tests/
 * @see http://www1.paybox.com/espace-integrateur-documentation/cartes-de-tests/
 */
class PayboxDirectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Returns a valid MMYY string for today's date.
     */
    private function getCardValidity()
    {
        return gmdate('my', time() + 86400);
    }

    /**
     * @return PayboxDirect
     */
    private function getTestPayboxDirectInstance()
    {
        $paybox = new Paybox(
            '1999888',
            '63',
            '109518543',
            '0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF'
        );

        return new PayboxDirect($paybox, PayboxDirect::PAYBOX_PREPROD_URL);
    }

    /**
     * @param PayboxDirectResponse $response The response to check.
     * @param string   $expectedStatus       The expected Response::* status constant.
     */
    private function assertResponseStatus(PayboxDirectResponse $response, $expectedStatus)
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
     * @param array  $amount
     * @param string $expectedStatus
     */
    public function testAuthorize($cardNumber, $validity, $cvv, array $amount, $expectedStatus)
    {
        $card = new Card($cardNumber, $validity, $cvv);
        $amount = Money::of(...$amount);
        $reference = __FUNCTION__ . '-' . time();

        $paybox = $this->getTestPayboxDirectInstance();

        $request = new Authorize($card, $amount, $reference);
        $response = $paybox->execute($request);

        $this->assertResponseStatus($response, $expectedStatus);
    }

    /**
     * @return array
     */
    public function providerAuthorize()
    {
        $exp = $this->getCardValidity();

        return [
            ['1111222233334444', $exp, '123', [10, 'EUR'], PayboxDirectResponse::SUCCESS],
            ['1111222233334444', '0101', '123', [10, 'EUR'], PayboxDirectResponse::INVALID_EXPIRY_DATE],
            ['1111222233335555', $exp, '123', [10, 'EUR'], PayboxDirectResponse::INVALID_CARD_NUMBER],
        ];
    }

    /**
     * @depends testAuthorize
     */
    public function testCapture()
    {
        $exp = $this->getCardValidity();

        $card = new Card('1111222233334444', $exp, '123');
        $amount = Money::of(10, 'EUR');
        $reference = __FUNCTION__ . '-' . time();

        $paybox = $this->getTestPayboxDirectInstance();

        $request = new Authorize($card, $amount, $reference);
        $response = $paybox->execute($request);

        $this->assertResponseStatus($response, PayboxDirectResponse::SUCCESS);

        $request = new Capture($amount, $reference, $response->getNumappel(), $response->getNumtrans());
        $response = $paybox->execute($request);

        $this->assertResponseStatus($response, PayboxDirectResponse::SUCCESS);
    }

    /**
     * @dataProvider providerAuthorizeAndCapture
     *
     * @param string $cardNumber
     * @param string $validity
     * @param string $cvv
     * @param array  $amount
     * @param string $expectedStatus
     */
    public function testAuthorizeAndCapture($cardNumber, $validity, $cvv, array $amount, $expectedStatus)
    {
        $card = new Card($cardNumber, $validity, $cvv);
        $amount = Money::of(...$amount);
        $reference = __FUNCTION__ . '-' . time();

        $paybox = $this->getTestPayboxDirectInstance();

        $request = new AuthorizeAndCapture($card, $amount, $reference);
        $response = $paybox->execute($request);

        $this->assertResponseStatus($response, $expectedStatus);
    }

    /**
     * @return array
     */
    public function providerAuthorizeAndCapture()
    {
        $exp = $this->getCardValidity();

        return [
            ['1111222233334444', $exp, '123', [10, 'EUR'], PayboxDirectResponse::SUCCESS],
            ['1111222233334444', '0101', '123', [10, 'EUR'], PayboxDirectResponse::INVALID_EXPIRY_DATE],
            ['1111222233335555', $exp, '123', [10, 'EUR'], PayboxDirectResponse::INVALID_CARD_NUMBER],
        ];
    }

    /**
     * @depends testAuthorizeAndCapture
     */
    public function testCancel()
    {
        $exp = $this->getCardValidity();

        $card = new Card('1111222233334444', $exp, '123');
        $amount = Money::of(10, 'EUR');
        $reference = __FUNCTION__ . '-' . time();

        $paybox = $this->getTestPayboxDirectInstance();

        $request = new AuthorizeAndCapture($card, $amount, $reference);
        $response = $paybox->execute($request);

        $this->assertResponseStatus($response, PayboxDirectResponse::SUCCESS);

        $request = new Cancel($amount, $reference, $response->getNumappel(), $response->getNumtrans());
        $response = $paybox->execute($request);

        $this->assertResponseStatus($response, PayboxDirectResponse::SUCCESS);
    }

    /**
     * @depends testAuthorizeAndCapture
     */
    public function testCheckExistingTransaction()
    {
        $exp = $this->getCardValidity();

        $card = new Card('1111222233334444', $exp, '123');
        $amount = Money::of(10, 'EUR');
        $reference = __FUNCTION__ . '-' . time();

        $paybox = $this->getTestPayboxDirectInstance();

        $request = new AuthorizeAndCapture($card, $amount, $reference);
        $captureResponse = $paybox->execute($request);

        $this->assertResponseStatus($captureResponse, PayboxDirectResponse::SUCCESS);

        $request = new CheckTransactionExistence($amount, $reference);
        $response = $paybox->execute($request);

        $this->assertResponseStatus($response, PayboxDirectResponse::SUCCESS);

        $this->assertSame($captureResponse->getNumappel(), $response->getNumappel());
        $this->assertSame($captureResponse->getNumtrans(), $response->getNumtrans());
    }

    /**
     * @depends testCheckExistingTransaction
     */
    public function testCheckNonExistingTransaction()
    {
        $amount = Money::of(10, 'EUR');
        $reference = __FUNCTION__ . '-' . time();

        $paybox = $this->getTestPayboxDirectInstance();

        $request = new CheckTransactionExistence($amount, $reference);
        $response = $paybox->execute($request);

        $this->assertResponseStatus($response, PayboxDirectResponse::TRANSACTION_NOT_FOUND);
    }

    /**
     * @dataProvider providerCredit
     *
     * @param string $cardNumber
     * @param string $validity
     * @param string $cvv
     * @param array  $amount
     * @param string $expectedStatus
     */
    public function testCredit($cardNumber, $validity, $cvv, array $amount, $expectedStatus)
    {
        $card = new Card($cardNumber, $validity, $cvv);
        $amount = Money::of(...$amount);
        $reference = __FUNCTION__ . '-' . time();

        $paybox = $this->getTestPayboxDirectInstance();

        $request = new Credit($card, $amount, $reference);
        $response = $paybox->execute($request);

        $this->assertResponseStatus($response, $expectedStatus);
    }

    /**
     * @return array
     */
    public function providerCredit()
    {
        $exp = $this->getCardValidity();

        return [
            ['4012001037141112', $exp, '123', [10, 'EUR'], PayboxDirectResponse::SUCCESS],
            ['4012001037141112', '0101', '123', [10, 'EUR'], PayboxDirectResponse::INVALID_EXPIRY_DATE],
            ['4012001037141113', $exp, '123', [10, 'EUR'], PayboxDirectResponse::INVALID_CARD_NUMBER],
        ];
    }

    public function testInquire()
    {
        $exp = $this->getCardValidity();

        $card = new Card('1111222233334444', $exp, '123');
        $amount = Money::of(10, 'EUR');
        $reference = __FUNCTION__ . '-' . time();

        $paybox = $this->getTestPayboxDirectInstance();

        $request = new AuthorizeAndCapture($card, $amount, $reference);
        $captureResponse = $paybox->execute($request);

        $this->assertResponseStatus($captureResponse, PayboxDirectResponse::SUCCESS);

        $request = new Inquire($captureResponse->getNumtrans());
        $response = $paybox->execute($request);

        $this->assertResponseStatus($response, PayboxDirectResponse::SUCCESS);

        $this->assertSame($captureResponse->getNumappel(), $response->getNumappel());
        $this->assertSame($captureResponse->getNumtrans(), $response->getNumtrans());

        $this->assertSame(utf8_decode('Capturé'), $response->getStatus());
    }

    /**
     * @depends testInquire
     */
    public function testInquireNonExistingTransaction()
    {
        $paybox = $this->getTestPayboxDirectInstance();

        $request = new Inquire('0000000000');
        $response = $paybox->execute($request);
        $this->assertResponseStatus($response, PayboxDirectResponse::TRANSACTION_NOT_FOUND);
    }
}
