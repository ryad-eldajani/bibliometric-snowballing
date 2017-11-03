<?php
/*
 * This file is part of the Bibliometric Snowballing project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace BS\Typo3;


use BS\App;

class Typo3System
{
    protected $classLoader = null;
    protected $application = null;

    /**
     * System constructor.
     */
    public function __construct(App $app)
    {
        $this->classLoader = require $app->getConfig('typo3/directory') . '/typo3_src/vendor/autoload.php';
        $this->application = new Application($this->classLoader);
        $this->application->run();
    }

    /**
     * Returns the Typo3 class loader.
     * @return \Composer\Autoload\ClassLoader instance
     */
    public function getClassLoader()
    {
        return $this->classLoader;
    }
}
