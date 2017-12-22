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
use BS\Helper\DataTypeHelper;

final class DataTypeHelperTest extends TestCase
{
    /**
     * @var DataTypeHelper $helper ValidatorHelper instance
     */
    protected $helper = null;

    /**
     * DataTypeHelper constructor.
     *
     * @param string $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->helper = DataTypeHelper::instance();;
    }

    /**
     * Test DataTypeHelper::get().
     */
    public function testGet()
    {
        $this->assertEquals(false, $this->helper->get('a', 'int', true));
        $this->assertEquals(0, $this->helper->get('a', 'int', false));
        $this->assertEquals(1, $this->helper->get('1', 'int', true));
        $this->assertEquals(false, $this->helper->get('1a', 'int', true));
        $this->assertEquals(1, $this->helper->get('1a', 'int', false));

        $this->assertEquals(false, $this->helper->get('a', 'double', true));
        $this->assertEquals(0.0, $this->helper->get('a', 'double', false));
        $this->assertEquals(1.0, $this->helper->get('1.0', 'double', true));
        $this->assertEquals(false, $this->helper->get('1a', 'double', true));
        $this->assertEquals(1.0, $this->helper->get('1a', 'double', false));
        $this->assertEquals(false, $this->helper->get('', 'double', true));
        $this->assertEquals(1.0, $this->helper->get('1', 'double', false));

        $this->assertEquals(true, $this->helper->get('1', 'bool', true));
        $this->assertEquals(true, $this->helper->get('1', 'bool', false));
        $this->assertEquals(false, $this->helper->get('1a', 'bool', true));
        $this->assertEquals(true, $this->helper->get('1a', 'bool', false));
        $this->assertEquals(false, $this->helper->get('0', 'bool', true));
        $this->assertEquals(false, $this->helper->get('0', 'bool', false));
        $this->assertEquals(false, $this->helper->get('0a', 'bool', true));
        $this->assertEquals(true, $this->helper->get('0a', 'bool', false));
        $this->assertEquals(false, $this->helper->get('', 'bool', true));
    }

    /**
     * Test DataTypeHelper::getArray().
     */
    public function testGetArray()
    {
        $this->assertEquals(
            array(1, 0, false, false),
            $this->helper->getArray(
                array('1', '0', '', '1a'),
                'int',
                true
            )
        );

        $this->assertEquals(
            array(1, 0, 0, 1),
            $this->helper->getArray(
                array('1', '0', '', '1a'),
                'int',
                false
            )
        );

        $this->assertEquals(
            array(),
            $this->helper->getArray(
                null,
                'int',
                false
            )
        );

        $this->assertEquals(
            array(),
            $this->helper->getArray(
                array(''),
                'int',
                false
            )
        );
    }
}
