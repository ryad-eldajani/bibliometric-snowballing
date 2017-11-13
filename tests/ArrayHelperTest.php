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
use BS\Helper\ArrayHelper;

class ArrayHelperTest extends TestCase
{
    /**
     * @var ArrayHelper $helper ArrayHelper instance
     */
    protected $helper = null;

    /**
     * ArrayHelperTest constructor.
     *
     * @param null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->helper = ArrayHelper::instance();
    }

    /**
     * Tests the ArrayHelper::override() method.
     */
    public function testOverride()
    {
        $array1 = array(
            'a' => 'A',
            'b' => array(
                'x' => 'X',
                'y' => 'Y',
                'z' => array(),
            )
        );

        $array2 = array(
            'a' => true,
            'b' => array(
                'x' => 'Z',
                'z' => 0
            )
        );

        $arrayExpected = array(
            'a' => true,
            'b' => array(
                'x' => 'Z',
                'y' => 'Y',
                'z' => 0
            )
        );
        $this->assertEquals(
            $arrayExpected,
            $this->helper->override($array1, $array2)
        );
    }

    /**
     * Tests the ArrayHelper::getValueByPath() method.
     */
    public function testGetValueByPath()
    {
        $array = array(
            'a' => array(
                'b' => array(
                    'c' => 1
                ),
                'd' => 2,
            ),
            'e' => 3
        );

        $this->assertEquals(1, $this->helper->getValueByPath($array, 'a/b/c'));
        $this->assertEquals(2, $this->helper->getValueByPath($array, 'a/d'));
        $this->assertEquals(3, $this->helper->getValueByPath($array, 'e'));
    }
}
