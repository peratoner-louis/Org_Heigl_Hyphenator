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
 * @category  Org_Heigl
 * @package   Org_Heigl_Hyphenator
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.1
 * @since     02.11.2011
 */
namespace Org\Heigl\HyphenatorTest;

use Org\Heigl\Hyphenator\Dictionary\Dictionary;
use Org\Heigl\Hyphenator\Dictionary\DictionaryRegistry;
use Org\Heigl\Hyphenator\Exception\PathNotDirException;
use Org\Heigl\Hyphenator\Exception\PathNotFoundException;
use Org\Heigl\Hyphenator\Filter\CustomMarkupFilter;
use Org\Heigl\Hyphenator\Filter\FilterRegistry;
use Org\Heigl\Hyphenator\Filter\SimpleFilter;
use Org\Heigl\Hyphenator\Hyphenator;
use Org\Heigl\Hyphenator\Options;
use Org\Heigl\Hyphenator\Tokenizer\PunctuationTokenizer;
use Org\Heigl\Hyphenator\Tokenizer\TokenizerRegistry;
use Org\Heigl\Hyphenator\Tokenizer\WhitespaceTokenizer;
use Org\Heigl\HyphenatorTest\Tokenizer\WhitespaceTokenizerTest;
use PHPUnit\Framework\TestCase;

/**
 * This class tests the functionality of the class Org_Heigl_Hyphenator
 *
 * @category  Org_Heigl
 * @package   Org_Heigl_Hyphenator
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.1
 * @since     20.04.2009
 */
final class HyphenatorTest extends TestCase
{
    public function testCreatingHyphenatorReturnsInstance(): void
    {
        $hyphenator = new Hyphenator();
        $this->assertInstanceOf(Hyphenator::class, $hyphenator);
        $this->assertInstanceOf(DictionaryRegistry::class, $hyphenator->getDictionaries());
        $this->assertInstanceOf(FilterRegistry::class, $hyphenator->getFilters());
        $this->assertInstanceOf(TokenizerRegistry::class, $hyphenator->getTokenizers());
    }

    public function testSettingOptions(): void
    {
        $hyphenator = new Hyphenator();
        $options = new Options();
        $this->assertInstanceof(Options::class, $hyphenator->getOptions());
        $this->assertNotSame($options, $hyphenator->getOptions());
        $hyphenator->setOptions($options);
        $this->assertSame($options, $hyphenator->getOptions());
        $this->assertCount(0, $hyphenator->getTokenizers());
        $options->addTokenizer('whitespace');
        $this->assertSame($hyphenator, $hyphenator->setOptions($options));
        $this->assertSame($options, $hyphenator->getOptions());
        $this->assertCount(1, $hyphenator->getTokenizers());
    }

    public function testGettingOption(): void
    {
        $h = new Hyphenator();
        $o = $h->getOptions();
        $this->assertInstanceOf(Options::class, $o);
        $this->assertSame($o, $h->getOptions());
    }

    public function testSettingDictionaries(): void
    {
        $hyphenator = new Hyphenator();
        $dict = new Dictionary('de');

        $this->assertInstanceOf(DictionaryRegistry::class, $hyphenator->getDictionaries());
        $this->assertSame($hyphenator, $hyphenator->addDictionary($dict));
        $this->assertCount(2, $hyphenator->getDictionaries());
        $this->assertSame($hyphenator, $hyphenator->addDictionary('en_US'));
        $this->assertCount(3, $hyphenator->getDictionaries());
    }
    public function testSettingTokenizers(): void
    {
        $hyphenator = new Hyphenator();
        $dict = new WhitespaceTokenizer();
        $hyphenator->addTokenizer($dict);
        $this->assertInstanceOf(TokenizerRegistry::class, $hyphenator->getTokenizers());
        $this->assertEquals($dict, $hyphenator->getTokenizers()->getTokenizerWithKey(0));
    }
    public function testSettingFilters(): void
    {
        $h = new Hyphenator();
        $h->getOptions()->setFilters(array());
        $f = new SimpleFilter();
        $this->assertInstanceOf(FilterRegistry::class, $h->getFilters());
        $this->assertCount(0, $h->getFilters());
        $this->assertSame($h, $h->addFilter($f));
        $this->assertInstanceof(FilterRegistry::class, $h->getFilters());
        $this->assertCount(1, $h->getFilters());
        $this->assertSame($f, $h->getFilters()->getFilterWithKey(0));
        $this->assertSame($h, $h->addFilter('CustomMarkup'));
        $this->assertInstanceof(FilterRegistry::class, $h->getFilters());
        $this->assertCount(2, $h->getFilters());
        $this->assertInstanceof(CustomMarkupFilter::class, $h->getFilters()->getFilterWithKey(1));
    }

    public function testHomeDirectory(): void
    {
        $h = new Hyphenator();
        $baseDirectory1 = dirname(__DIR__) . '/src/share';
        $this->assertEquals($baseDirectory1, Hyphenator::getDefaultHomePath());
        $this->assertEquals($baseDirectory1, $h->getHomePath());
        $baseDirectory2 = __DIR__ . '/share/tmp1';
        mkdir($baseDirectory2);
        putenv('HYPHENATOR_HOME=' . $baseDirectory2);
        $this->assertEquals($baseDirectory2, \Org\Heigl\Hyphenator\Hyphenator::getDefaultHomePath());
        $this->assertEquals($baseDirectory2, $h->getHomePath());
        rmdir($baseDirectory2);
        $this->assertEquals($baseDirectory1, \Org\Heigl\Hyphenator\Hyphenator::getDefaultHomePath());
        $this->assertEquals($baseDirectory1, $h->getHomePath());
        $baseDirectory3 = __DIR__ . '/share/tmp2';
        mkdir($baseDirectory3);
        define('HYPHENATOR_HOME', $baseDirectory3);
        $this->assertEquals($baseDirectory3, \Org\Heigl\Hyphenator\Hyphenator::getDefaultHomePath());
        $this->assertEquals($baseDirectory3, $h->getHomePath());
        rmdir($baseDirectory3);
        $this->assertEquals($baseDirectory1, \Org\Heigl\Hyphenator\Hyphenator::getDefaultHomePath());
        $this->assertEquals($baseDirectory1, $h->getHomePath());
        $baseDirectory4 = __DIR__ . '/share/tmp3';
        mkdir($baseDirectory4);
        \Org\Heigl\Hyphenator\Hyphenator::setDefaultHomePath($baseDirectory4);
        $this->assertEquals($baseDirectory4, \Org\Heigl\Hyphenator\Hyphenator::getDefaultHomePath());
        $this->assertEquals($baseDirectory4, $h->getHomePath());
        rmdir($baseDirectory4);
        $this->assertEquals($baseDirectory1, \Org\Heigl\Hyphenator\Hyphenator::getDefaultHomePath());
        $this->assertEquals($baseDirectory1, $h->getHomePath());
        $baseDirectory5 = __DIR__ . '/share/tmp4';
        mkdir($baseDirectory5);
        $h->setHomePath($baseDirectory5);
        $this->assertEquals($baseDirectory5, $h->getHomePath());
        rmdir($baseDirectory5);
        $this->assertEquals($baseDirectory1, $h->getHomePath());
    }

    public function testSettingDefaultHomeDirectoryWithInvalidInfos(): void
    {
        $h = new Hyphenator();
        try {
            $h->setHomePath('foo');
            $this->fail('No Exception thrown');
        } catch (\Exception $e) {
            $this->assertInstanceof(PathNotFoundException::class, $e);
        }
        try {
            $h->setHomePath(__FILE__);
            $this->fail('No Exception thrown');
        } catch (\Exception $e) {
            $this->assertInstanceof(PathNotDirException::class, $e);
        }
        try {
            Hyphenator::setDefaultHomePath('foo');
            $this->fail('No Exception thrown');
        } catch (\Exception $e) {
            $this->assertInstanceof(PathNotFoundException::class, $e);
        }
        try {
            Hyphenator::setDefaultHomePath(__FILE__);
            $this->fail('No Exception thrown');
        } catch (\Exception $e) {
            $this->assertInstanceof(PathNotDirException::class, $e);
        }
    }

    public function testAutoloading(): void
    {
        $this->assertFalse(Hyphenator::__autoload('stuff'));
        $this->assertFalse(Hyphenator::__autoload('Org\Heigl\Hyphenator\Filters'));
        $this->assertFalse(Hyphenator::__autoload('Org\Heigl\Hyphenator\Onlyfortesting'));
        $this->assertTrue(Hyphenator::__autoload('Org\Heigl\Hyphenator\Anotheronefortesting'));
    }

    public function testRegisteringAutoload(): void
    {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('Autoloading is not tested on HHVM');
        }
        spl_autoload_unregister(array('Org\Heigl\Hyphenator\Hyphenator', '__autoload'));
        //$this->assertNotContains(array('Org\Heigl\Hyphenator\Hyphenator', '__autoload'),spl_autoload_functions());
        Hyphenator::registerAutoload();
        $this->assertContains(array('Org\Heigl\Hyphenator\Hyphenator', '__autoload'), spl_autoload_functions());
    }

    public function testHyphenatorInvocationSimple(): void
    {
        $h = Hyphenator::factory(__DIR__ . '/share/test2', 'de_DE');
        $h->getOptions()->setHyphen('-');

        $this->assertInstanceof(TokenizerRegistry::class, $h->getTokenizers());
        $t = $h->getTokenizers();
        $this->assertEquals(new WhitespaceTokenizer(), $t->getTokenizerWithKey(0));
        $this->assertEquals(new PunctuationTokenizer(), $t->getTokenizerWithKey(1));
        $this->assertEquals('Do-nau-dampf-schiff-fahrt', $h->hyphenate('Donaudampfschifffahrt'));
        $this->assertEquals('Gü-ter-mäd-chen', $h->hyphenate('Gütermädchen'));
    }

    public function testHyphenatorInvocationWithoutFactory(): void
    {
        $o = new Options();
        $o->setHyphen('-')
          ->setDefaultLocale('de_DE')
          ->setRightMin(2)
          ->setLeftMin(2)
          ->setWordMin(5)
          ->setFilters('Simple')
          ->setTokenizers('Whitespace', 'Punctuation');
        Dictionary::setFileLocation(__DIR__ . '/share/test3/files/dictionaries/');
        $h = new Hyphenator();
        $h->setOptions($o);

        $this->assertEquals(
            'We have some re-al-ly long words in ger-man like sau-er-stoff-feld-fla-sche.',
            $h->hyphenate('We have some really long words in german like sauerstofffeldflasche.')
        );
    }

    public function testSpecialSpaceChar(): void
    {
        $o = new Options();
        $o->setHyphen('-')
          ->setDefaultLocale('fr')
          ->setFilters('Simple')
          ->setTokenizers('Whitespace', 'Punctuation');
        $h = new Hyphenator();
        $h->setOptions($o);

        $this->assertEquals(
            'Ceci est à rem-pla-cer par une fâble'."\xE2\x80\xAF".':p',
            $h->hyphenate('Ceci est à remplacer par une fâble'."\xE2\x80\xAF".':p')
        );
    }

    public function testSettingTokenizers2(): void
    {
        $options = $this->createMock(\Org\Heigl\Hyphenator\Options::class);
        $options->expects($this->exactly(2))
            ->method('getTokenizers')
            ->willReturn(array('Whitespace', 'Punctuation'));

        $h = new Hyphenator();
        $h->setOptions($options);

        $ref = new \ReflectionClass($h);
        $prop = $ref->getProperty('tokenizers');
        $prop->setAccessible(true);
        $prop->setValue($h, new TokenizerRegistry());

        $result = $h->getTokenizers();

        $this->assertCount(2, $result);
        $this->assertInstanceof(WhitespaceTokenizer::class, $result->current());
    }
}
