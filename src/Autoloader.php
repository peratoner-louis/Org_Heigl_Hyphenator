<?php
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
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.1
 * @link      http://github.com/heiglandreas/Hyphenator
 * @since     02.11.2011
 */

namespace Org\Heigl\Hyphenator;

use Org\Heigl\Hyphenator\Dictionary\Dictionary;
use Org\Heigl\Hyphenator\Dictionary\DictionaryRegistry;
use Org\Heigl\Hyphenator\Exception\PathNotDirException;
use Org\Heigl\Hyphenator\Exception\PathNotFoundException;
use Org\Heigl\Hyphenator\Filter\Filter;
use Org\Heigl\Hyphenator\Filter\FilterRegistry;
use Org\Heigl\Hyphenator\Tokenizer\Token;
use Org\Heigl\Hyphenator\Tokenizer\Tokenizer;
use Org\Heigl\Hyphenator\Tokenizer\TokenizerRegistry;
use Org\Heigl\Hyphenator\Tokenizer\TokenRegistry;
use Org\Heigl\Hyphenator\Tokenizer\WordToken;

final class Autoloader
{
    /** @var self */
    private static $instance;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        // do nothing
    }
    /**
     * autoload classes.
     *
     * @param string $className the name of the class to load
     *
     * @return bool
     */
    public function __invoke(string $className)
    {
        if (0 !== strpos($className, 'Org\\Heigl\\Hyphenator')) {
            return false;
        }
        $className = substr($className, strlen('Org\\Heigl\\Hyphenator\\'));
        $file = str_replace('\\', '/', $className) . '.php';
        $fileName = __DIR__ . DIRECTORY_SEPARATOR . $file;
        if (! file_exists(realpath($fileName))) {
            return false;
        }
        if (! @include_once $fileName) {
            return false;
        }

        return true;
    }
}
