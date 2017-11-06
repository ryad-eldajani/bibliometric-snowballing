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

/**
 * This is a project-specific implementation of the PSR-4 autoloader.
 * Based on: http://www.php-fig.org/psr/psr-4/examples/
 *
 * @param string $class The fully-qualified class name.
 * @return void
 */
spl_autoload_register(function ($class) {
    // If class starts with "TYPO3\", move to the TYPO3 autoloader.
    if (strncmp('TYPO3\\', $class, strlen('TYPO3\\')) === 0) {
        return;
    }

    // Get the relative class name (remove "BS\" if necessary)
    $relativeClass = str_replace('BS\\', '', $class);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = __DIR__ . DIRECTORY_SEPARATOR
        . str_replace('\\', '/', $relativeClass) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});
