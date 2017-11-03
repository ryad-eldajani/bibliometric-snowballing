<?php
namespace BS\Typo3;

use BS\App;

class ConfigurationManager extends \TYPO3\CMS\Core\Configuration\ConfigurationManager
{
    /**
     * ConfigurationManager constructor.
     */
    public function __construct()
    {
        $typo3Dir = App::instance()->getConfig('typo3/directory');
        $this->localConfigurationFile = $typo3Dir. '/typo3conf/LocalConfiguration.php';
        $this->defaultConfigurationFile = $typo3Dir . '/typo3/sysext/core/Configuration/DefaultConfiguration.php';
    }

    /**
     * Reads the configuration array and exports it to the global variable
     *
     * @access private
     * @throws \UnexpectedValueException
     */
    public function exportConfiguration()
    {
        if (@is_file($this->getLocalConfigurationFileLocation())) {
            $localConfiguration = $this->getLocalConfiguration();
            if (is_array($localConfiguration)) {
                $defaultConfiguration = $this->getDefaultConfiguration();
                \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($defaultConfiguration, $localConfiguration);
                $GLOBALS['TYPO3_CONF_VARS'] = $defaultConfiguration;
            } else {
                throw new \UnexpectedValueException('LocalConfiguration invalid.', 1349272276);
            }
            if (@is_file($this->getAdditionalConfigurationFileLocation())) {
                require $this->getAdditionalConfigurationFileLocation();
            }
        } else {
            // No LocalConfiguration (yet), load DefaultConfiguration only
            $GLOBALS['TYPO3_CONF_VARS'] = $this->getDefaultConfiguration();
        }
    }

    /**
     * Get the file location of the default configuration file,
     * currently the path and filename.
     *
     * @return string
     * @access private
     */
    public function getDefaultConfigurationFileLocation()
    {
        return $this->defaultConfigurationFile;
    }

    /**
     * Get the file location of the local configuration file,
     * currently the path and filename.
     *
     * @return string
     * @access private
     */
    public function getLocalConfigurationFileLocation()
    {
        return $this->localConfigurationFile;
    }
}
