<?php

/**
 * Copyright 2010-2026 Horde LLC (http://www.horde.org/)
 *
 * @category   Horde
 * @copyright  2010-2016 Horde LLC
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package    Mime
 * @subpackage UnitTests
 */

namespace Horde\Mime\Test\Unnamespaced\ContentParam;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Horde_Mime_ContentParam_Decode;

/**
 * Tests for the Horde_Mime_ContentParam_Decode class.
 *
 * @author     Michael Slusarz <slusarz@horde.org>
 * @category   Horde
 * @copyright  2010-2016 Horde LLC
 * @internal
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package    Mime
 * @subpackage UnitTests
 * @coversNothing
 */
class DecodeTest extends TestCase
{
    /**
     * @dataProvider decodeProvider
     */
    #[DataProvider('decodeProvider')]
    public function testDecode($string, $expected)
    {
        $decode = new Horde_Mime_ContentParam_Decode();
        $res = $decode->decode($string);

        ksort($res);

        $this->assertEquals(
            $expected,
            $res
        );
    }

    public static function decodeProvider()
    {
        return [
            [
                'foo=bar',
                [
                    'foo' => 'bar',
                ],
            ],
            [
                'foofoo=b',
                [
                    'foofoo' => 'b',
                ],
            ],
            [
                'f=barbar',
                [
                    'f' => 'barbar',
                ],
            ],
            [
                'foo=bar; a=b',
                [
                    'a' => 'b',
                    'foo' => 'bar',
                ],
            ],
            [
                '  foo =    bar    ; a     =b ;c   =   d   ',
                [
                    'a' => 'b',
                    'c' => 'd',
                    'foo' => 'bar',
                ],
            ],
            // Malformed unquoted value starting with '(' (RFC 2045 violation:
            // '(' is a tspecial and only valid inside a quoted-string).
            // Real-world example seen in the wild:
            //   Content-Disposition: inline; filename=(BA_AE_Cobrand no icon.png
            // The parent Horde_Mail_Rfc822 SkipLwsp interprets '(' as a CFWS
            // comment opener and threw when no ')' was found, killing the
            // whole MIME parse. Lenient content-param parsing must not
            // throw, and should preserve the malformed value rather than
            // drop the parameter — capture everything up to the next ';'.
            [
                ' filename=(BA_AE_Cobrand no icon.png',
                [
                    'filename' => '(BA_AE_Cobrand no icon.png',
                ],
            ],
            [
                'foo=bar; filename=(BA_AE_Cobrand no icon.png',
                [
                    'filename' => '(BA_AE_Cobrand no icon.png',
                    'foo' => 'bar',
                ],
            ],
        ];
    }

}
