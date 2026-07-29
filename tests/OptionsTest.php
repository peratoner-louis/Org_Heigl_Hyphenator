<?php

declare(strict_types=1);

/**
 * Copyright (c) 2008-2011 Andreas Heigl<andreas@heigl.org>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category  Hyphenator
 * @package   Org\Heigl\Hyphenator
 * @subpackage Tests
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.1
 * @since     02.11.2011
 */
namespace Org\Heigl\HyphenatorTest;

use Org\Heigl\Hyphenator\Options;
use Org\Heigl\Hyphenator\Tokenizer\TokenRegistry;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

/**
 * This class tests the functionality of the class Org_Heigl_Hyphenator
 *
 * @category  Hyphenator
 * @package   Org\Heigl\Hyphenator
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.1
 * @since     02.11.2011
 */
final class OptionsTest extends TestCase
{
    public function testSettingHyphen(): void
    {
        $o = new Options();
        $this->assertEquals(chr(173), $o->getHyphen());
        $this->assertSame($o, $o->setHyphen('test'));
        $this->assertEquals('test', $o->getHyphen());
    }

    public function testSettingNoHyphenateString(): void
    {
        $o = new Options();
        $this->assertEquals('', $o->getNoHyphenateString());
        $this->assertSame($o, $o->setNoHyphenateString('test'));
        $this->assertEquals('test', $o->getNoHyphenateString());
    }

    public function testSettingLeftMin(): void
    {
        $o = new Options();
        $this->assertSame(2, $o->getLeftMin());
        $this->assertSame($o, $o->setLeftMin('test'));
        $this->assertSame(0, $o->getLeftMin());
        $this->assertSame($o, $o->setLeftMin(5));
        $this->assertSame(5, $o->getLeftMin());
    }

    public function testSettingRightMin(): void
    {
        $o = new Options();
        $this->assertSame(2, $o->getRightMin());
        $this->assertSame($o, $o->setRightMin('test'));
        $this->assertSame(0, $o->getRightMin());
        $this->assertSame($o, $o->setRightMin(5));
        $this->assertSame(5, $o->getRightMin());
    }


    public function testSettingMinWordSize(): void
    {
        $o = new Options();
        $this->assertSame(6, $o->getMinWordLength());
        $this->assertSame($o, $o->setMinWordLength(''));
        $this->assertSame(0, $o->getMinWordLength());
        $this->assertSame($o, $o->setMinWordLength(PHP_INT_MAX));
        $this->assertSame(PHP_INT_MAX, $o->getMinWordLength());
    }

    public function testSettingCustomHyphen(): void
    {
        $o = new Options;
        $this->assertEquals('--', $o->getCustomHyphen());
        $this->assertSame($o, $o->setCustomHyphen('++'));
        $this->assertEquals('++', $o->getCustomHyphen());
    }

    public function testSettingFilters(): void
    {
        $o = new Options();
        $this->assertSame([], $o->getFilters());
        $this->assertSame($o, $o->setFilters(''));
        $this->assertSame([], $o->getFilters());
        $this->assertSame($o, $o->setFilters('filterA, filterB'));
        $this->assertSame(['filterA', 'filterB'], $o->getFilters());
        $this->assertSame($o, $o->setFilters(''));
        $this->assertSame([], $o->getFilters());
        $this->assertSame($o, $o->setFilters(['filterC','filterD']));
        $this->assertSame(['filterC','filterD'], $o->getFilters());
    }

    public function testSettingFilterInstance(): void
    {
        $o = new Options();

        $filter = new class extends \Org\Heigl\Hyphenator\Filter\Filter {
            public function run(TokenRegistry $tokens)
            {
                // Do nothing
            }

            protected function doConcatenate(TokenRegistry $tokens)
            {
                // do nothing
            }
        };

        $this->assertSame([], $o->getFilters());
        $this->assertSame($o, $o->addFilter($filter));
        $this->assertSame([$filter], $o->getFilters());
    }

    /**
     * @dataProvider settingSomethingElseThanFilterFailsProvider
     */
    public function testSettingSomethingElseThanFilterFails($filter): void
    {
        $this->expectException(UnexpectedValueException::class);
        $o = new Options();

        $o->addFilter($filter);
    }

    public static function settingSomethingElseThanFilterFailsProvider(): \Iterator
    {
        yield array(new \stdClass());
        yield array(new \Exception());
    }

    public function testSettingTokenizerInstance(): void
    {
        $o = new Options();

        $tokenizer = new class implements \Org\Heigl\Hyphenator\Tokenizer\Tokenizer {

            public function run($input)
            {
                // Do nothing
            }
        };

        $this->assertSame([], $o->getTokenizers());
        $this->assertSame($o, $o->addTokenizer($tokenizer));
        $this->assertSame([$tokenizer], $o->getTokenizers());
    }
    /**
     * @dataProvider settingSoemthingElseThanTokenizerFailsProvider
     */
    public function testSettingSoemthingElseThanTokenizerFails($tokenizer): void
    {
        $this->expectException(UnexpectedValueException::class);
        $o = new Options();

        $o->addTokenizer($tokenizer);
    }

    public static function settingSoemthingElseThanTokenizerFailsProvider(): \Iterator
    {
        yield array(new \stdClass());
        yield array(new \Exception());
    }


    public function testSettingTokenizers(): void
    {
        $o = new Options();
        $this->assertSame(array(), $o->getTokenizers());
        $this->assertSame($o, $o->setTokenizers(''));
        $this->assertSame(array(), $o->getTokenizers());
        $this->assertSame($o, $o->setTokenizers('filterA, filterB'));
        $this->assertSame(array('filterA', 'filterB'), $o->getTokenizers());
        $this->assertSame($o, $o->setTokenizers(''));
        $this->assertSame(array(), $o->getTokenizers());
        $this->assertSame($o, $o->setTokenizers(array('filterC','filterD')));
        $this->assertSame(array('filterC','filterD'), $o->getTokenizers());
    }

    public function testCreatingOptionViaFactory(): void
    {
        try {
            Options::factory('foo');
            $this->fail('Foo should not be readable');
        } catch (\Org\Heigl\Hyphenator\Exception\PathNotFoundException $e) {
            $this->assertTrue(true);
        }
        try {
            Options::factory(__DIR__ . '/share/unparseable.ini');
            $this->fail('The given file should not be parseable');
        } catch (\Org\Heigl\Hyphenator\Exception\InvalidArgumentException $e) {
            $this->assertTrue(true);
        }
        $o = Options::factory(__DIR__ . '/share/onlydist.ini');
        $this->assertInstanceof('\Org\Heigl\Hyphenator\Options', $o);
        $o = Options::factory(__DIR__ . '/share/parseable.ini');
        $this->assertInstanceof('\Org\Heigl\Hyphenator\Options', $o);
        $this->assertSame('test', $o->getHyphen());
        $this->assertSame('test', $o->getNoHyphenateString());
        $this->assertSame(5, $o->getLeftMin());
        $this->assertSame(5, $o->getRightMin());
        $this->assertSame(5, $o->getMinWordLength());
        $this->assertSame(5, $o->getQuality());
        $this->assertSame('test', $o->getCustomHyphen());
        $this->assertEquals(['test1', 'test2'], $o->getTokenizers());
        $this->assertEquals(['test3', 'test4'], $o->getFilters());
        $this->assertSame('test', $o->getDefaultLocale());
    }

    public function testDefaultLocale(): void
    {
        $o = new Options();
        $this->assertEquals('en_EN', $o->getDefaultLocale());
        $this->assertSame($o, $o->setDefaultLocale('test'));
        $this->assertEquals('test', $o->getDefaultLocale());
    }
}
