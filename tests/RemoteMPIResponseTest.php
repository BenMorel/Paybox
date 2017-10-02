<?php

namespace Paybox\Tests;

use Paybox\RemoteMPIResponse;

use PHPUnit\Framework\TestCase;

/**
 * Tests for the RemoteMPIResponse class.
 */
class RemoteMPIResponseTest extends TestCase
{
    /**
     * @dataProvider providerResponse
     *
     * @param array  $data      An associative array of data as returned by the authentication server.
     * @param int    $status    The expected RemoteMPIResponse::STATUS_* constant.
     * @param string $sessionId The expected session ID.
     * @param string $id3d      The expected authentication ID.
     */
    public function testResponse(array $data, $status, $sessionId, $id3d)
    {
        $response = new RemoteMPIResponse($data);

        $this->assertSame($status, $response->getStatus());
        $this->assertSame($sessionId, $response->getSessionId());
        $this->assertSame($id3d, $response->getID3D());
    }

    /**
     * @return array
     */
    public function providerResponse()
    {
        return [
            [[], RemoteMPIResponse::STATUS_UNKNOWN, '', ''],
            [['IdSession' => '123'], RemoteMPIResponse::STATUS_UNKNOWN, '123', ''],
            [['IdSession' => '123', 'ID3D' => '456'], RemoteMPIResponse::STATUS_UNKNOWN, '123', '456'],
            [['3DSTATUS' => 'U'], RemoteMPIResponse::STATUS_UNKNOWN, '', ''],
            [['3DSTATUS' => 'N'], RemoteMPIResponse::STATUS_UNKNOWN, '', ''],
            [['3DSTATUS' => 'Y'], RemoteMPIResponse::STATUS_UNKNOWN, '', ''],
            [['3DENROLLED' => 'U'], RemoteMPIResponse::STATUS_UNKNOWN, '', ''],
            [['3DENROLLED' => 'U', '3DSTATUS' => 'U'], RemoteMPIResponse::STATUS_UNKNOWN, '', ''],
            [['3DENROLLED' => 'U', '3DSTATUS' => 'N'], RemoteMPIResponse::STATUS_UNKNOWN, '', ''],
            [['3DENROLLED' => 'U', '3DSTATUS' => 'Y'], RemoteMPIResponse::STATUS_UNKNOWN, '', ''],
            [['3DENROLLED' => 'N'], RemoteMPIResponse::STATUS_NOT_ENROLLED, '', ''],
            [['3DENROLLED' => 'N', '3DSTATUS' => 'U'], RemoteMPIResponse::STATUS_NOT_ENROLLED, '', ''],
            [['3DENROLLED' => 'N', '3DSTATUS' => 'N'], RemoteMPIResponse::STATUS_NOT_ENROLLED, '', ''],
            [['3DENROLLED' => 'N', '3DSTATUS' => 'Y'], RemoteMPIResponse::STATUS_NOT_ENROLLED, '', ''],
            [['3DENROLLED' => 'Y'], RemoteMPIResponse::STATUS_ENROLLED_AUTH_FAILURE, '', ''],
            [['3DENROLLED' => 'Y', '3DSTATUS' => 'U'], RemoteMPIResponse::STATUS_ENROLLED_AUTH_FAILURE, '', ''],
            [['3DENROLLED' => 'Y', '3DSTATUS' => 'N'], RemoteMPIResponse::STATUS_ENROLLED_AUTH_FAILURE, '', ''],
            [['3DENROLLED' => 'Y', '3DSTATUS' => 'Y'], RemoteMPIResponse::STATUS_ENROLLED_AUTH_SUCCESS, '', ''],
            [['3DENROLLED' => 'Y', '3DSTATUS' => 'Y', 'ID3D' => '333', 'IdSession' => '222'], RemoteMPIResponse::STATUS_ENROLLED_AUTH_SUCCESS, '222', '333'],
        ];
    }
}
