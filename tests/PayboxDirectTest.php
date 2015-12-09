<?php

namespace Paybox\Tests;

use Paybox\Card;
use Paybox\Paybox;
use Paybox\PayboxDirect;
use Paybox\Request\Authorize;
use Paybox\Request\AuthorizeAndCapture;
use Paybox\Request\Cancel;
use Paybox\Request\Capture;
use Paybox\Request\CheckTransactionExistence;
use Paybox\Request\Credit;
use Paybox\Request\Inquire;
use Paybox\Response;

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
        return [
            ['1111222233334444', '1216', '123', 'EUR 10', Response::SUCCESS],
            ['1111222233334444', '0101', '123', 'EUR 10', Response::INVALID_EXPIRY_DATE],
            ['1111222233335555', '1216', '123', 'EUR 10', Response::INVALID_CARD_NUMBER],
        ];
    }

    /**
     * @depends testAuthorize
     */
    public function testCapture()
    {
        $card = new Card('1111222233334444', '1216', '123');
        $amount = Money::of(10, 'EUR');
        $reference = __FUNCTION__ . '-' . time();

        $paybox = $this->getTestPayboxDirectInstance();

        $request = new Authorize($card, $amount, $reference);
        $response = $paybox->execute($request);

        $this->assertResponseStatus($response, Response::SUCCESS);

        $request = new Capture($amount, $reference, $response->getNumappel(), $response->getNumtrans());
        $response = $paybox->execute($request);

        $this->assertResponseStatus($response, Response::SUCCESS);
    }

    /**
     * @dataProvider providerAuthorizeAndCapture
     *
     * @param string $cardNumber
     * @param string $validity
     * @param string $cvv
     * @param string $amount
     * @param string $expectedStatus
     */
    public function testAuthorizeAndCapture($cardNumber, $validity, $cvv, $amount, $expectedStatus)
    {
        $card = new Card($cardNumber, $validity, $cvv);
        $amount = Money::parse($amount);
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
        return [
            ['1111222233334444', '1216', '123', 'EUR 10', Response::SUCCESS],
            ['1111222233334444', '0101', '123', 'EUR 10', Response::INVALID_EXPIRY_DATE],
            ['1111222233335555', '1216', '123', 'EUR 10', Response::INVALID_CARD_NUMBER],
        ];
    }

    /**
     * @depends testAuthorizeAndCapture
     */
    public function testCancel()
    {
        $card = new Card('1111222233334444', '1216', '123');
        $amount = Money::of(10, 'EUR');
        $reference = __FUNCTION__ . '-' . time();

        $paybox = $this->getTestPayboxDirectInstance();

        $request = new AuthorizeAndCapture($card, $amount, $reference);
        $response = $paybox->execute($request);

        $this->assertResponseStatus($response, Response::SUCCESS);

        $request = new Cancel($amount, $reference, $response->getNumappel(), $response->getNumtrans());
        $response = $paybox->execute($request);

        $this->assertResponseStatus($response, Response::SUCCESS);
    }

    /**
     * @depends testAuthorizeAndCapture
     */
    public function testCheckExistingTransaction()
    {
        $card = new Card('1111222233334444', '1216', '123');
        $amount = Money::of(10, 'EUR');
        $reference = __FUNCTION__ . '-' . time();

        $paybox = $this->getTestPayboxDirectInstance();

        $request = new AuthorizeAndCapture($card, $amount, $reference);
        $captureResponse = $paybox->execute($request);

        $this->assertResponseStatus($captureResponse, Response::SUCCESS);

        $request = new CheckTransactionExistence($amount, $reference);
        $response = $paybox->execute($request);

        $this->assertResponseStatus($response, Response::SUCCESS);

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

        $this->assertResponseStatus($response, Response::TRANSACTION_NOT_FOUND);
    }

    /**
     * @dataProvider providerCredit
     *
     * @param string $cardNumber
     * @param string $validity
     * @param string $cvv
     * @param string $amount
     * @param string $expectedStatus
     */
    public function testCredit($cardNumber, $validity, $cvv, $amount, $expectedStatus)
    {
        $card = new Card($cardNumber, $validity, $cvv);
        $amount = Money::parse($amount);
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
        return [
            ['4012001037141112', '1216', '123', 'EUR 10', Response::SUCCESS],
            ['4012001037141112', '0101', '123', 'EUR 10', Response::INVALID_EXPIRY_DATE],
            ['4012001037141113', '1216', '123', 'EUR 10', Response::INVALID_CARD_NUMBER],
        ];
    }

    public function testInquire()
    {
        $card = new Card('1111222233334444', '1216', '123');
        $amount = Money::of(10, 'EUR');
        $reference = __FUNCTION__ . '-' . time();

        $paybox = $this->getTestPayboxDirectInstance();

        $request = new AuthorizeAndCapture($card, $amount, $reference);
        $captureResponse = $paybox->execute($request);

        $this->assertResponseStatus($captureResponse, Response::SUCCESS);

        $request = new Inquire($captureResponse->getNumtrans());
        $response = $paybox->execute($request);

        $this->assertResponseStatus($response, Response::SUCCESS);

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
        $this->assertResponseStatus($response, Response::TRANSACTION_NOT_FOUND);
    }
}
