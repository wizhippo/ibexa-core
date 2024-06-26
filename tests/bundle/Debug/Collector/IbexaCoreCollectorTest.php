<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Tests\Bundle\Debug\Collector;

use Exception;
use Ibexa\Bundle\Debug\Collector\IbexaCoreCollector;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

class IbexaCoreCollectorTest extends TestCase
{
    /** @var \Ibexa\Bundle\Debug\Collector\IbexaCoreCollector */
    private $mainCollector;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mainCollector = new IbexaCoreCollector();
    }

    public function testAddGetCollector()
    {
        $collector = $this->getDataCollectorMock();
        $name = 'foobar';
        $collector
            ->expects(self::once())
            ->method('getName')
            ->will(self::returnValue($name));

        $this->mainCollector->addCollector($collector);
        self::assertSame($collector, $this->mainCollector->getCollector($name));
    }

    public function testGetInvalidCollector()
    {
        $this->expectException(\InvalidArgumentException::class);

        $collector = $this->getDataCollectorMock();
        $this->mainCollector->addCollector($collector);
        self::assertSame($collector, $this->mainCollector->getCollector('foo'));
    }

    public function testGetAllCollectors()
    {
        $collector1 = $this->getDataCollectorMock();
        $nameCollector1 = 'collector1';
        $collector1
            ->expects(self::once())
            ->method('getName')
            ->will(self::returnValue($nameCollector1));
        $collector2 = $this->getDataCollectorMock();
        $nameCollector2 = 'collector2';
        $collector2
            ->expects(self::once())
            ->method('getName')
            ->will(self::returnValue($nameCollector2));

        $allCollectors = [
            $nameCollector1 => $collector1,
            $nameCollector2 => $collector2,
        ];

        foreach ($allCollectors as $name => $collector) {
            $this->mainCollector->addCollector($collector);
        }

        self::assertSame($allCollectors, $this->mainCollector->getAllCollectors());
    }

    public function testGetToolbarTemplateNothing()
    {
        $collector = $this->getDataCollectorMock();
        $name = 'foobar';
        $collector
            ->expects(self::once())
            ->method('getName')
            ->will(self::returnValue($name));
        $this->mainCollector->addCollector($collector);
        self::assertNull($this->mainCollector->getToolbarTemplate($name));
    }

    public function testGetToolbarTemplate()
    {
        $collector = $this->getDataCollectorMock();
        $name = 'foobar';
        $collector
            ->expects(self::once())
            ->method('getName')
            ->will(self::returnValue($name));
        $toolbarTemplate = 'toolbar.html.twig';

        $this->mainCollector->addCollector($collector, 'foo', $toolbarTemplate);
        self::assertSame($toolbarTemplate, $this->mainCollector->getToolbarTemplate($name));
    }

    public function testGetPanelTemplateNothing()
    {
        $collector = $this->getDataCollectorMock();
        $name = 'foobar';
        $collector
            ->expects(self::once())
            ->method('getName')
            ->will(self::returnValue($name));
        $this->mainCollector->addCollector($collector);
        self::assertNull($this->mainCollector->getPanelTemplate($name));
    }

    public function testGetPanelTemplate()
    {
        $collector = $this->getDataCollectorMock();
        $name = 'foobar';
        $collector
            ->expects(self::once())
            ->method('getName')
            ->will(self::returnValue($name));
        $panelTemplate = 'toolbar.html.twig';

        $this->mainCollector->addCollector($collector, $panelTemplate, 'foo');
        self::assertSame($panelTemplate, $this->mainCollector->getPanelTemplate($name));
    }

    public function testCollect()
    {
        $collector1 = $this->getDataCollectorMock();
        $nameCollector1 = 'collector1';
        $collector1
            ->expects(self::once())
            ->method('getName')
            ->will(self::returnValue($nameCollector1));
        $collector2 = $this->getDataCollectorMock();
        $nameCollector2 = 'collector2';
        $collector2
            ->expects(self::once())
            ->method('getName')
            ->will(self::returnValue($nameCollector2));

        $allCollectors = [
            $nameCollector1 => $collector1,
            $nameCollector2 => $collector2,
        ];

        $request = new Request();
        $response = new Response();
        $exception = new Exception();

        /** @var \PHPUnit\Framework\MockObject\MockObject */
        foreach ($allCollectors as $name => $collector) {
            $this->mainCollector->addCollector($collector);
            $collector
                ->expects(self::once())
                ->method('collect')
                ->with($request, $response, $exception);
        }

        $this->mainCollector->collect($request, $response, $exception);
    }

    protected function getDataCollectorMock()
    {
        return $this->createMock(DataCollectorInterface::class);
    }
}
