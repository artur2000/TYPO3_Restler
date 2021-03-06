<?php

namespace Aoe\Restler\Tests\Unit\System;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 AOE GmbH <dev@aoe.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Aoe\Restler\System\Dispatcher;
use Aoe\Restler\System\Restler\Builder;
use Aoe\Restler\Tests\Unit\BaseTest;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package Restler
 * @subpackage Tests
 *
 * @covers \Aoe\Restler\System\Dispatcher
 */
class DispatcherTest extends BaseTest
{
    /**
     * @var Dispatcher
     */
    protected $dispatcher;
    /**
     * @var Builder
     */
    protected $restlerBuilder;
    /**
     * @var ObjectManager
     */
    protected $objectManager;
    /**
     * setup
     */
    protected function setUp()
    {
        if (interface_exists('\Psr\Http\Server\MiddlewareInterface')) {
            parent::setUp();
            $this->restlerBuilder = $this->getMockBuilder('Aoe\\Restler\\System\\Restler\\Builder')
                ->disableOriginalConstructor()->getMock();
            $this->objectManager = $this->getMockBuilder('TYPO3\\CMS\\Extbase\\Object\\ObjectManager')
                ->disableOriginalConstructor()->getMock();
            $this->objectManager->expects($this->atLeastOnce())->method('get')->will($this->returnValue($this->restlerBuilder));

            $this->dispatcher = new Dispatcher($this->objectManager);
        } else {
            $this->markTestSkipped("No MiddlewareInterface");
        }
    }

    /**
     * @test
     */
    public function canProcessToTypo3()
    {
        $requestUri = $this->getMockBuilder('TYPO3\\CMS\\Core\\Http\\Uri')->getMock();
        $requestUri->method('getPath')->willReturn("/api/device");
        $requestUri->method('withQuery')->willReturn($requestUri);
        $requestUri->method('withPath')->willReturn($requestUri);

        $request = $this->getMockBuilder('Psr\\Http\\Message\\ServerRequestInterface')->getMock();
        $request->method('getUri')->willReturn($requestUri);

        $handler = $this->getMockBuilder('Psr\\Http\\Server\\RequestHandlerInterface')->getMock();
        $handler->expects($this->once())->method('handle');

        $this->dispatcher->process($request, $handler);
    }
}
