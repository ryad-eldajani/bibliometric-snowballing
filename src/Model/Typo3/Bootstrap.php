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

namespace BS\Model\Typo3;

use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;

class Bootstrap extends \TYPO3\CMS\Core\Core\Bootstrap
{
    /**
     * Main entry point called at every request usually from Global scope. Checks if everything is correct,
     * and loads the Configuration.
     *
     * Make sure that the baseSetup() is called before and the class loader is present
     *
     * @return Bootstrap
     */
    public function configure()
    {
        $this->startOutputBuffering()
            ->loadConfigurationAndInitialize()
            ->unsetReservedGlobalVariables()
            ->initializeTypo3DbGlobal();

        return $this;
    }

    /**
     * We need an early instance of the configuration manager.
     * Since makeInstance relies on the object configuration, we create it here with new instead.
     *
     * @return Bootstrap
     * @internal This is not a public API method, do not use in own extensions
     */
    public function populateLocalConfiguration()
    {
        try {
            $configurationManager = $this->getEarlyInstance(ConfigurationManager::class);
        } catch (\TYPO3\CMS\Core\Exception $exception) {
            $configurationManager = new ConfigurationManager();
            $this->setEarlyInstance(ConfigurationManager::class, $configurationManager);
        }
        $configurationManager->exportConfiguration();
        return $this;
    }

    /**
     * Includes LocalConfiguration.php and sets several
     * global settings depending on configuration.
     *
     * @param bool $allowCaching Whether to allow caching - affects cache_core (autoloader)
     * @param string $packageManagerClassName Define an alternative package manager implementation (usually for the installer)
     * @return Bootstrap
     * @internal This is not a public API method, do not use in own extensions
     */
    public function loadConfigurationAndInitialize($allowCaching = true, $packageManagerClassName = \TYPO3\CMS\Core\Package\PackageManager::class)
    {
        $this->populateLocalConfiguration();

        return $this;
    }

    /**
     * Run the base setup that checks server environment, determines paths,
     * populates base files and sets common configuration.
     *
     * Script execution will be aborted if something fails here.
     *
     * @param int $entryPointLevel Number of subdirectories where the entry script is located under the document root
     * @return Bootstrap
     * @throws \RuntimeException when TYPO3_REQUESTTYPE was not set before, setRequestType() needs to be called before
     * @internal This is not a public API method, do not use in own extensions
     */
    public function baseSetup($entryPointLevel = 0)
    {
        if (!defined('TYPO3_REQUESTTYPE')) {
            throw new \RuntimeException('No Request Type was set, TYPO3 does not know in which context it is run.', 1450561838);
        }
        SystemEnvironmentBuilder::run($entryPointLevel);

        return $this;
    }
}
