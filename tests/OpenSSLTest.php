<?php

namespace Paybox\Tests;

use Paybox\OpenSSL;

/**
 * Tests for the OpenSSL class.
 */
class OpenSSLTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerCheckSignature
     *
     * @param string $message        The message to check.
     * @param bool   $expectedResult The expected result.
     */
    public function testCheckSignature($message, $expectedResult)
    {
        $openssl = new OpenSSL();
        $result = $openssl->checkSignature($message);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function providerCheckSignature()
    {
        return [
            ['IdSession=session002&StatusPBX=Timeout&3DSTATUS=N&3DSIGNVAL=Y&3DENROLLED=Y&3DERROR=0&3DXID=ej77Ax2su6mBwEvI7v7eTWbjPBo=&ID3D=2000000267972&Check=SLGxbGYJNFMoX0ZujWWU%2fwdLbpp4MUojlcTkK3iA6wXGsiU692slTZp7PYcjzlraoU2A8AfPS9HGVHIVLbS8IYVy3uZsMnXRyWbjJybmnfL6PqTI9yjuZEG56FJVRSH2V0zb1yU2JpwG5SdDl7t3yBl2MFAiyo2UG3QTLOsy8Ao%3d', true],
            ['IdSession=session002&StatusPBX=Timeout&3DSTATUS=Y&3DSIGNVAL=Y&3DENROLLED=Y&3DERROR=0&3DXID=ej77Ax2su6mBwEvI7v7eTWbjPBo=&ID3D=2000000267972&Check=SLGxbGYJNFMoX0ZujWWU%2fwdLbpp4MUojlcTkK3iA6wXGsiU692slTZp7PYcjzlraoU2A8AfPS9HGVHIVLbS8IYVy3uZsMnXRyWbjJybmnfL6PqTI9yjuZEG56FJVRSH2V0zb1yU2JpwG5SdDl7t3yBl2MFAiyo2UG3QTLOsy8Ao%3d', false],
            ['IdSession=session002&StatusPBX=Timeout&3DSTATUS=N&3DSIGNVAL=Y&3DENROLLED=Y&3DERROR=0&3DXID=ej77Ax2su6mBwEvI7v7eTWbjPBo=&ID3D=2000000267972&Check=SLGxbGYJNFMoX0ZujWWU%2fwdLbpp4MUojlcTkK3iA6wXGsiU692slTZp7PYcjzlraoU2A8AfPS9HGVHIVLbS8IYVy3uZsMnXRyWbjJybmnfL6PqTI9yjuZEG56FJVRSH2V0zb1yU2JpwG5SdDl7t3yBl2MFAiyo2UG3QTLOsy8Bo%3d', false],
            ['IdSession=1448876207&StatusPBX=Timeout&3DSTATUS=N&3DSIGNVAL=Y&3DENROLLED=Y&3DERROR=0&3DXID=ZeA3onBxBw9dkhCjS6rGv6f+Kd4=&ID3D=2000000273403&Check=Z1S5tq60WHzdGHfeDoIdkgaqpz614Zl1qnqQo4dHiaSHx4NW%2byk8iXOj1rJ1f1L7lvGU2DtsxPvgk34dKOHDg5fOkKZiGNgkN05L10VybvjlgfwE4ryDwnQA%2fDkM1DW1aZxyvloJLVwNwyRyZuFu1QeIICLUJoXflDOHXmrvtzw%3d', true],
            ['IdSession=1448876207&StatusPBX=Timeout&3DSTATUS=N&3DSIGNVAL=Y&3DENROLLED=Y&3DERROR=0&3DXID=ZeA3onBxBw9dkhCjS6rGv6f+Kd4=&ID3D=2000000273403&Check=Z1S5tq60WHzdGHfeDoIdkgaqpz614Zl1qnqQo4dHiaSHx4NW%2byk8iXOj1rJ1f1L7lvGU2DtsxPvgk34dKOHDg5fOkKZiGNgkN05L10VybvjlgfwE4ryDwnQA%2fDkM1DW1aZxyvloJLVwNwyRyZuFu1QeIICLUJoXflDOHXmrvtza%3d', false],
            ['IdSession=1448876207&StatusPBX=Timeout&3DSTATUS=N&3DSIGNVAL=Y&3DENROLLED=N&3DERROR=0&3DXID=ZeA3onBxBw9dkhCjS6rGv6f+Kd4=&ID3D=2000000273403&Check=Z1S5tq60WHzdGHfeDoIdkgaqpz614Zl1qnqQo4dHiaSHx4NW%2byk8iXOj1rJ1f1L7lvGU2DtsxPvgk34dKOHDg5fOkKZiGNgkN05L10VybvjlgfwE4ryDwnQA%2fDkM1DW1aZxyvloJLVwNwyRyZuFu1QeIICLUJoXflDOHXmrvtzw%3d', false],
        ];
    }
}
