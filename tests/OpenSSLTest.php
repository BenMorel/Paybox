<?php

namespace Paybox\Tests;

use Paybox\OpenSSL;

use PHPUnit\Framework\TestCase;

/**
 * Tests for the OpenSSL class.
 */
class OpenSSLTest extends TestCase
{
    /**
     * @dataProvider providerCheckSignature
     *
     * @param string $message        The message to check.
     * @param bool   $isPost         Whether the message comes from a POST request.
     * @param bool   $expectedResult The expected result.
     */
    public function testCheckSignature($message, $isPost, $expectedResult)
    {
        $openssl = new OpenSSL();
        $result = $openssl->checkSignature($message, $isPost);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function providerCheckSignature()
    {
        return [
            // Ensure that no errors are thrown for malformed messages
            ['', false, false],
            ['', true, false],
            ['a', false, false],
            ['a', true, false],
            ['a=', false, false],
            ['a=', true, false],
            ['a=b', false, false],
            ['a=b', true, false],
            ['a=b&', false, false],
            ['a=b&', true, false],
            ['a=b&c', false, false],
            ['a=b&c', true, false],
            ['a=b&c=', false, false],
            ['a=b&c=', true, false],
            ['a=b&c=d', false, false],
            ['a=b&c=', true, false],

            // Check valid and forged callback URL messages
            ['IdSession=session002&StatusPBX=Timeout&3DSTATUS=N&3DSIGNVAL=Y&3DENROLLED=Y&3DERROR=0&3DXID=ej77Ax2su6mBwEvI7v7eTWbjPBo=&ID3D=2000000267972&Check=SLGxbGYJNFMoX0ZujWWU%2fwdLbpp4MUojlcTkK3iA6wXGsiU692slTZp7PYcjzlraoU2A8AfPS9HGVHIVLbS8IYVy3uZsMnXRyWbjJybmnfL6PqTI9yjuZEG56FJVRSH2V0zb1yU2JpwG5SdDl7t3yBl2MFAiyo2UG3QTLOsy8Ao%3d', false, true],
            ['IdSession=session002&StatusPBX=Timeout&3DSTATUS=Y&3DSIGNVAL=Y&3DENROLLED=Y&3DERROR=0&3DXID=ej77Ax2su6mBwEvI7v7eTWbjPBo=&ID3D=2000000267972&Check=SLGxbGYJNFMoX0ZujWWU%2fwdLbpp4MUojlcTkK3iA6wXGsiU692slTZp7PYcjzlraoU2A8AfPS9HGVHIVLbS8IYVy3uZsMnXRyWbjJybmnfL6PqTI9yjuZEG56FJVRSH2V0zb1yU2JpwG5SdDl7t3yBl2MFAiyo2UG3QTLOsy8Ao%3d', false, false],
            ['IdSession=session002&StatusPBX=Timeout&3DSTATUS=N&3DSIGNVAL=Y&3DENROLLED=Y&3DERROR=0&3DXID=ej77Ax2su6mBwEvI7v7eTWbjPBo=&ID3D=2000000267972&Check=SLGxbGYJNFMoX0ZujWWU%2fwdLbpp4MUojlcTkK3iA6wXGsiU692slTZp7PYcjzlraoU2A8AfPS9HGVHIVLbS8IYVy3uZsMnXRyWbjJybmnfL6PqTI9yjuZEG56FJVRSH2V0zb1yU2JpwG5SdDl7t3yBl2MFAiyo2UG3QTLOsy8Bo%3d', false, false],

            ['IdSession=1448876207&StatusPBX=Timeout&3DSTATUS=N&3DSIGNVAL=Y&3DENROLLED=Y&3DERROR=0&3DXID=ZeA3onBxBw9dkhCjS6rGv6f+Kd4=&ID3D=2000000273403&Check=Z1S5tq60WHzdGHfeDoIdkgaqpz614Zl1qnqQo4dHiaSHx4NW%2byk8iXOj1rJ1f1L7lvGU2DtsxPvgk34dKOHDg5fOkKZiGNgkN05L10VybvjlgfwE4ryDwnQA%2fDkM1DW1aZxyvloJLVwNwyRyZuFu1QeIICLUJoXflDOHXmrvtzw%3d', false, true],
            ['IdSession=1448876207&StatusPBX=Timeout&3DSTATUS=N&3DSIGNVAL=Y&3DENROLLED=Y&3DERROR=0&3DXID=ZeA3onBxBw9dkhCjS6rGv6f+Kd4=&ID3D=2000000273403&Check=Z1S5tq60WHzdGHfeDoIdkgaqpz614Zl1qnqQo4dHiaSHx4NW%2byk8iXOj1rJ1f1L7lvGU2DtsxPvgk34dKOHDg5fOkKZiGNgkN05L10VybvjlgfwE4ryDwnQA%2fDkM1DW1aZxyvloJLVwNwyRyZuFu1QeIICLUJoXflDOHXmrvtza%3d', false, false],
            ['IdSession=1448876207&StatusPBX=Timeout&3DSTATUS=N&3DSIGNVAL=Y&3DENROLLED=N&3DERROR=0&3DXID=ZeA3onBxBw9dkhCjS6rGv6f+Kd4=&ID3D=2000000273403&Check=Z1S5tq60WHzdGHfeDoIdkgaqpz614Zl1qnqQo4dHiaSHx4NW%2byk8iXOj1rJ1f1L7lvGU2DtsxPvgk34dKOHDg5fOkKZiGNgkN05L10VybvjlgfwE4ryDwnQA%2fDkM1DW1aZxyvloJLVwNwyRyZuFu1QeIICLUJoXflDOHXmrvtzw%3d', false, false],

            ['IdSession=1449094098&StatusPBX=Autorisation%20%e0%20faire&3DSTATUS=Y&3DSIGNVAL=Y&3DENROLLED=Y&3DERROR=0&3DECI=05&3DXID=0qk87PdWNjJJL3eKQt6k4fATkbw=&3DCAVV=AAABB3FWUgQZBhJQSVZSAAAAAAA=&3DCAVVALGO=2&ID3D=1000012364347&Check=EXb84Y6Mk7VLm2kve60WvgwevvJm8a%2bzvbzF%2flF3iLi6wiUXc7%2fySupZ57oJfXmN3wi%2bU%2b82S2GBjB1HRaqZ15VihjtVqh8c2xmPviZ37Q8rzNMfDYbxC3%2bQfCTUfMTYNWHqQT4dVREBFFU7Fvv2QuhawImi%2b2Kj1T0JbLbmmx8%3d', false, true],
            ['IdSession=1449094098&StatusPBX=Autorisation%20%e0%20faire&3DSTATUS=N&3DSIGNVAL=Y&3DENROLLED=Y&3DERROR=0&3DECI=05&3DXID=0qk87PdWNjJJL3eKQt6k4fATkbw=&3DCAVV=AAABB3FWUgQZBhJQSVZSAAAAAAA=&3DCAVVALGO=2&ID3D=1000012364347&Check=EXb84Y6Mk7VLm2kve60WvgwevvJm8a%2bzvbzF%2flF3iLi6wiUXc7%2fySupZ57oJfXmN3wi%2bU%2b82S2GBjB1HRaqZ15VihjtVqh8c2xmPviZ37Q8rzNMfDYbxC3%2bQfCTUfMTYNWHqQT4dVREBFFU7Fvv2QuhawImi%2b2Kj1T0JbLbmmx8%3d', false, false],

            // Check valid and forged return URL messages
            ['IdSession=1449094098&StatusPBX=Autorisation+%E0+faire&3DSTATUS=Y&3DSIGNVAL=Y&3DENROLLED=Y&3DERROR=0&3DECI=05&3DXID=0qk87PdWNjJJL3eKQt6k4fATkbw%3D&3DCAVV=AAABB3FWUgQZBhJQSVZSAAAAAAA%3D&3DCAVVALGO=2&ID3D=1000012364347&Check=EXb84Y6Mk7VLm2kve60WvgwevvJm8a%252bzvbzF%252flF3iLi6wiUXc7%252fySupZ57oJfXmN3wi%252bU%252b82S2GBjB1HRaqZ15VihjtVqh8c2xmPviZ37Q8rzNMfDYbxC3%252bQfCTUfMTYNWHqQT4dVREBFFU7Fvv2QuhawImi%252b2Kj1T0JbLbmmx8%253d', true, true],
            ['IdSession=1449094098&StatusPBX=Autorisation+%E0+faire&3DSTATUS=N&3DSIGNVAL=Y&3DENROLLED=Y&3DERROR=0&3DECI=05&3DXID=0qk87PdWNjJJL3eKQt6k4fATkbw%3D&3DCAVV=AAABB3FWUgQZBhJQSVZSAAAAAAA%3D&3DCAVVALGO=2&ID3D=1000012364347&Check=EXb84Y6Mk7VLm2kve60WvgwevvJm8a%252bzvbzF%252flF3iLi6wiUXc7%252fySupZ57oJfXmN3wi%252bU%252b82S2GBjB1HRaqZ15VihjtVqh8c2xmPviZ37Q8rzNMfDYbxC3%252bQfCTUfMTYNWHqQT4dVREBFFU7Fvv2QuhawImi%252b2Kj1T0JbLbmmx8%253d', true, false],
        ];
    }

    /**
     * @expectedException \Paybox\OpenSSLException
     */
    public function testCheckSignatureWithInvalidPublicKeyFile()
    {
        $openssl = new OpenSSL();
        $openssl->checkSignature('', false, 'non-existent-file.pem');
    }
}
