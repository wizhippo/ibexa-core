<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Repository\Decorator;

use Ibexa\Contracts\Core\Repository\Decorator\LanguageServiceDecorator;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\LanguageCreateStruct;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LanguageServiceDecoratorTest extends TestCase
{
    protected function createDecorator(MockObject $service): LanguageService
    {
        return new class($service) extends LanguageServiceDecorator {
        };
    }

    protected function createServiceMock(): MockObject
    {
        return $this->createMock(LanguageService::class);
    }

    public function testCreateLanguageDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [$this->createMock(LanguageCreateStruct::class)];

        $serviceMock->expects(self::once())->method('createLanguage')->with(...$parameters);

        $decoratedService->createLanguage(...$parameters);
    }

    public function testUpdateLanguageNameDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            $this->createMock(Language::class),
            'random_value_5ced05ce0e4e45.35668562',
        ];

        $serviceMock->expects(self::once())->method('updateLanguageName')->with(...$parameters);

        $decoratedService->updateLanguageName(...$parameters);
    }

    public function testEnableLanguageDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [$this->createMock(Language::class)];

        $serviceMock->expects(self::once())->method('enableLanguage')->with(...$parameters);

        $decoratedService->enableLanguage(...$parameters);
    }

    public function testDisableLanguageDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [$this->createMock(Language::class)];

        $serviceMock->expects(self::once())->method('disableLanguage')->with(...$parameters);

        $decoratedService->disableLanguage(...$parameters);
    }

    public function testLoadLanguageDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = ['random_value_5ced05ce0e4f44.11240129'];

        $serviceMock->expects(self::once())->method('loadLanguage')->with(...$parameters);

        $decoratedService->loadLanguage(...$parameters);
    }

    public function testLoadLanguagesDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [];

        $serviceMock->expects(self::once())->method('loadLanguages')->with(...$parameters);

        $decoratedService->loadLanguages(...$parameters);
    }

    public function testLoadLanguageByIdDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [100];

        $serviceMock->expects(self::once())->method('loadLanguageById')->with(...$parameters);

        $decoratedService->loadLanguageById(...$parameters);
    }

    public function testLoadLanguageListByCodeDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [['random_value_5ced05ce0e4fb1.09879860']];

        $serviceMock->expects(self::once())->method('loadLanguageListByCode')->with(...$parameters);

        $decoratedService->loadLanguageListByCode(...$parameters);
    }

    public function testLoadLanguageListByIdDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [['random_value_5ced05ce0e4fd1.13022531']];

        $serviceMock->expects(self::once())->method('loadLanguageListById')->with(...$parameters);

        $decoratedService->loadLanguageListById(...$parameters);
    }

    public function testDeleteLanguageDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [$this->createMock(Language::class)];

        $serviceMock->expects(self::once())->method('deleteLanguage')->with(...$parameters);

        $decoratedService->deleteLanguage(...$parameters);
    }

    public function testGetDefaultLanguageCodeDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [];

        $serviceMock->expects(self::once())->method('getDefaultLanguageCode')->with(...$parameters);

        $decoratedService->getDefaultLanguageCode(...$parameters);
    }

    public function testNewLanguageCreateStructDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [];

        $serviceMock->expects(self::once())->method('newLanguageCreateStruct')->with(...$parameters);

        $decoratedService->newLanguageCreateStruct(...$parameters);
    }
}
