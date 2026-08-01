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
 * @subpackage Filter
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.1
 * @since     02.12.2011
 */
namespace Org\Heigl\HyphenatorTest\Filter;

use Org\Heigl\Hyphenator\Filter\CustomMarkupFilter;
use PHPUnit\Framework\TestCase;

/**
 * This class tests the functionality of the class NonStandardFilter
 *
 * @category  Hyphenator
 * @package   Org\Heigl\Hyphenator
 * @subpackage Filter
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.1
 * @since     02.12.2011
 */
final class CustomMarkupFilterTest extends TestCase
{
    public function testConcatenation(): void
    {
        $obj = new CustomMarkupFilter();

        $token1 = $this->createMock(\Org\Heigl\Hyphenator\Tokenizer\Token::class);
        $token1->expects($this->once())
            ->method('getFilteredContent')
            ->willReturn('a');

        $token2 = $this->createMock(\Org\Heigl\Hyphenator\Tokenizer\Token::class);
        $token2->expects($this->once())
            ->method('getFilteredContent')
            ->willReturn('b');

        $tokenList = new \Org\Heigl\Hyphenator\Tokenizer\TokenRegistry();
        $tokenList->add($token1);
        $tokenList->add($token2);

        $method = \UnitTestHelper::getMethod($obj, 'concatenate');
        $result = $method->invoke($obj, $tokenList);

        $this->assertEquals('ab', $result);
    }
}
