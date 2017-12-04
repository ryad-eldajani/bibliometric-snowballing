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

namespace BS\Model;

use BS\Helper\ArrayHelper;
use BS\Helper\TemplateHelper;
use BS\Model\Http\Http;
use BS\Model\Typo3\Typo3System;
use BS\Model\User\UserManager;
use League\Plates\Engine;

class App
{
    /**
     * @var App $instance Singleton instance
     */
    protected static $instance = null;

    /**
     * @var array $config Application configuration
     */
    protected $config = null;

    /**
     * @var Typo3System $typo3System Typo3 hook
     */
    protected $typo3System = null;

    /**
     * @var Engine $templateEngine Plates template engine
     */
    protected $templateEngine = null;

    /**
     * @var array $urlInfo URL information about controllers etc.
     */
    protected $urlInfo = null;

    /**
     * App constructor.
     */
    private function __construct()
    {
        set_exception_handler(array($this, 'handleException'));
        $this->templateEngine = new Engine('templates');
        $this->templateEngine->loadExtension(new TemplateHelper());
    }

    /**
     * Returns the singleton.
     *
     * @param bool $typo3SetupInProgress If true, the Typo3 system will not setup
     * @return App instance
     */
    public static function instance($typo3SetupInProgress = false)
    {
        if (!isset(App::$instance)) {
            App::$instance = new App();
        }

        if (!$typo3SetupInProgress && !isset(App::$instance->typo3System)) {
            App::$instance->typo3System = new Typo3System(App::$instance);
        }

        return App::$instance;
    }

    /**
     * Global exception handler.
     * @param \Throwable $exception throwable instance
     */
    public function handleException($exception)
    {
        echo $this->renderTemplate('exception', array('exception' => $exception));
        exit(1);
    }

    /**
     * Loads the configuration.
     */
    protected function loadConfiguration()
    {
        if (is_array($this->config)) {
            return;
        }

        // load default configuration
        $jsonDefault = file_get_contents('conf/config.default.json');
        $this->config = \json_decode($jsonDefault, true);

        // load custom configuration, if existing
        if (file_exists('conf/config.json')) {
            $jsonCustom = file_get_contents('conf/config.json');
            $this->config = ArrayHelper::instance()->override(
                $this->config,
                \json_decode($jsonCustom, true)
            );
        }
    }

    /**
     * Returns a configuration setting by path.
     * E.g. $path = 'db/hostname' returns the database hostname.
     *
     * @param string $path configuration path
     * @return mixed Configuration value
     */
    public function getConfig($path)
    {
        if (!isset($this->config)) {
            $this->loadConfiguration();
        }

        return ArrayHelper::instance()->getValueByPath($this->config, $path);
    }

    /**
     * Returns the Typo3 system instance.
     * @return Typo3System instance
     */
    public function getTypo3System()
    {
        return $this->typo3System;
    }

    /**
     * Renders a template using the template Engine.
     *
     * @param string $templateName template name
     * @param array|null $templateParameters parameters for the template
     * @return string rendered template
     */
    public function renderTemplate($templateName, array $templateParameters = null)
    {
        if (UserManager::instance()->isLoggedIn()) {
            $templateParameters['user'] = UserManager::instance()
                ->getUserInformation();
        }

        $templateParameters['request'] = Http::instance()->getRequestInfo();
        $this->templateEngine->addData($templateParameters);

        return $this->templateEngine->render($templateName);
    }
}
