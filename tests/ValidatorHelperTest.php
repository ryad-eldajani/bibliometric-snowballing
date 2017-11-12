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

use PHPUnit\Framework\TestCase;
use BS\Model\Http\Http;
use BS\Helper\ValidatorHelper;

final class ValidatorHelperTest extends TestCase
{
    /**
     * @var Http $http Http instance
     */
    protected $http = null;

    /**
     * @var ValidatorHelper $helper ValidatorHelper instance
     */
    protected $helper = null;

    /**
     * ValidatorHelperTest constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $http = Http::instance();
        $helper = ValidatorHelper::instance();

        // Mock POST parameters.
        $reflectionHttp = new ReflectionClass($http);
        $reflectionHttpProperty = $reflectionHttp->getProperty('requestInfo');
        $reflectionHttpProperty->setAccessible(true);
        $reflectionHttpProperty->setValue(
            $http,
            array(
                'post_params' => array(
                    'a' => '1',
                    'b' => '1.1',
                    'c' => '9999999999999999999999999999',
                    'd' => '99999999999999999999.9999999',
                    'e' => 'abc',
                    'f' => '  abc  ',
                    'g' => '0',
                    'h' => '',
                    'i' => 'aaaaaaaaaaaaaaaaaa',
                )
            )
        );

        // Set mocked Http instance for the ValidatorHelper.
        $reflectionHelper = new ReflectionClass($helper);
        $reflectionHelperProperty = $reflectionHelper->getProperty('http');
        $reflectionHelperProperty->setAccessible(true);
        $reflectionHelperProperty->setValue($helper, $http);

        $this->http = $http;
        $this->helper = $helper;
    }

    /**
     * Test validate() 'required' option.
     */
    public function testValidateRequired()
    {
        $this->assertFalse(
            $this->helper->validate(array('zzz' => array('required' => true)))
        );
        $this->assertTrue(
            $this->helper->validate(array('a' => array('required' => true)))
        );
    }

    /**
     * Test validate() 'func' option.
     */
    public function testValidateFunc()
    {
        $this->assertTrue(
            $this->helper->validate(array('f' => array('func' => function($x) { return trim($x); })))
        );
        $this->assertEquals('abc', $this->http->getPostParam('f'));

        $this->assertTrue(
            $this->helper->validate(array('e' => array('func' => function($x) { return strtoupper($x); })))
        );
        $this->assertEquals('ABC', $this->http->getPostParam('e'));
    }

    /**
     * Test validate() 'type' option.
     */
    public function testValidateType()
    {
        $this->assertTrue(
            $this->helper->validate(array('a' => array('type' => 'int')))
        );
        $this->assertTrue(
            $this->helper->validate(array('b' => array('type' => 'double')))
        );
        $this->assertTrue(
            $this->helper->validate(array('g' => array('type' => 'bool')))
        );
        $this->assertTrue(
            $this->helper->validate(array('e' => array('type' => 'string')))
        );
        $this->assertFalse(
            $this->helper->validate(array('e' => array('type' => 'int')))
        );
        $this->assertFalse(
            $this->helper->validate(array('e' => array('type' => 'double')))
        );
        $this->assertFalse(
            $this->helper->validate(array('e' => array('type' => 'bool')))
        );
        $this->assertFalse(
            $this->helper->validate(array('h' => array('type' => 'bool')))
        );
    }

    /**
     * Test validate() 'min/max' option.
     */
    public function testValidateMinMax()
    {
        $this->assertTrue(
            $this->helper->validate(array('a' => array('min' => 1)))
        );
        $this->assertFalse(
            $this->helper->validate(array('a' => array('min' => 2)))
        );
        $this->assertTrue(
            $this->helper->validate(array('a' => array('max' => 255)))
        );
        $this->assertFalse(
            $this->helper->validate(array('a' => array('max' => 0)))
        );
        $this->assertTrue(
            $this->helper->validate(array('i' => array('min' => 10)))
        );
        $this->assertFalse(
            $this->helper->validate(array('i' => array('min' => 20)))
        );
        $this->assertTrue(
            $this->helper->validate(array('i' => array('max' => 18)))
        );
        $this->assertFalse(
            $this->helper->validate(array('i' => array('max' => 17)))
        );
    }
}
