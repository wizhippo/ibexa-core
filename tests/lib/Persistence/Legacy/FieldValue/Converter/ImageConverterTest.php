<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Persistence\Legacy\FieldValue\Converter;

use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Core\IO\IOServiceInterface;
use Ibexa\Core\IO\UrlRedecoratorInterface;
use Ibexa\Core\IO\Values\BinaryFile;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\ImageConverter;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ClockMock;

final class ImageConverterTest extends TestCase
{
    /** @var \Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\ImageConverter */
    private $imageConverter;

    /** @var \Ibexa\Core\IO\UrlRedecoratorInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $urlRedecorator;

    /** @var \Ibexa\Core\IO\IOServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $ioService;

    protected function setUp(): void
    {
        $this->ioService = $this->createMock(IOServiceInterface::class);
        $this->urlRedecorator = $this->createMock(UrlRedecoratorInterface::class);

        $this->imageConverter = new ImageConverter(
            $this->ioService,
            $this->urlRedecorator
        );
    }

    /**
     * @dataProvider fieldValueToXmlProvider
     */
    public function testToStorageValue(FieldValue $fieldValue, string $expectedXml): void
    {
        ClockMock::register(ImageConverter::class);
        ClockMock::withClockMock(true);

        $time = ClockMock::time();
        $expectedXml = str_replace('{timestampToReplace}', (string)$time, $expectedXml);

        $storageValue = new StorageFieldValue();

        $pathToImg = __DIR__ . '/../_fixtures/ibexa_fav.png';
        $this
            ->urlRedecorator
            ->method('redecorateFromSource')
            ->willReturn($pathToImg);

        $this->imageConverter->toStorageValue($fieldValue, $storageValue);

        self::assertEquals(
            $expectedXml,
            $storageValue->dataText
        );

        ClockMock::withClockMock(false);
    }

    public function fieldValueToXmlProvider(): array
    {
        $pathToImg = __DIR__ . '/../_fixtures/ibexa_fav.png';
        $dir = __DIR__ . '/../_fixtures';

        return [
            'with_additional_data' => [
                new FieldValue([
                    'data' => [
                        'width' => 100,
                        'height' => 200,
                        'alternativeText' => 'test',
                        'mime' => 'image/png',
                        'fieldId' => 1,
                        'uri' => $pathToImg,
                        'versionNo' => 1,
                        'languageCode' => 'eng-GB',
                        'additionalData' => [
                            'focalPointX' => 50,
                            'focalPointY' => 100,
                            'author' => 'John Smith',
                        ],
                    ],
                ]),
                <<< XML
<?xml version="1.0" encoding="utf-8"?>
<ezimage serial_number="1" is_valid="1" filename="ibexa_fav.png"
    suffix="png" basename="ibexa_fav" dirpath="{$dir}" url="{$pathToImg}"
    original_filename="ibexa_fav.png" mime_type="image/png" width="100"
    height="200" alternative_text="test" alias_key="1293033771" timestamp="{timestampToReplace}">
  <original attribute_id="1" attribute_version="1" attribute_language="eng-GB"/>
  <information Height="200" Width="100" IsColor="1"/>
  <additional_data><attribute key="focalPointX">50</attribute><attribute key="focalPointY">100</attribute><attribute key="author">John Smith</attribute></additional_data>
</ezimage>
XML,
            ],
            'without_additional_data_stored' => [
                new FieldValue([
                    'data' => [
                        'width' => 100,
                        'height' => 200,
                        'alternativeText' => 'test',
                        'mime' => 'image/png',
                        'fieldId' => 1,
                        'uri' => $pathToImg,
                        'versionNo' => 1,
                        'languageCode' => 'eng-GB',
                    ],
                ]),
                <<< XML
<?xml version="1.0" encoding="utf-8"?>
<ezimage serial_number="1" is_valid="1" filename="ibexa_fav.png"
    suffix="png" basename="ibexa_fav" dirpath="{$dir}" url="{$pathToImg}"
    original_filename="ibexa_fav.png" mime_type="image/png" width="100"
    height="200" alternative_text="test" alias_key="1293033771" timestamp="{timestampToReplace}">
  <original attribute_id="1" attribute_version="1" attribute_language="eng-GB"/>
  <information Height="200" Width="100" IsColor="1"/>
  <additional_data/>
</ezimage>
XML,
            ],
        ];
    }

    /**
     * @dataProvider xmlToFieldValueProvider
     */
    public function testToFieldValue(string $xml, FieldValue $expectedFieldValue): void
    {
        ClockMock::register(ImageConverter::class);
        ClockMock::withClockMock(true);

        $time = ClockMock::time();
        $xml = str_replace('{timestampToReplace}', (string) $time, $xml);
        $storageValue = new StorageFieldValue([
            'dataText' => $xml,
        ]);

        $this
            ->ioService
            ->method('loadBinaryFileByUri')
            ->willReturn(new BinaryFile(['id' => 1]));

        $fieldValue = new FieldValue();
        $this->imageConverter->toFieldValue($storageValue, $fieldValue);

        self::assertEquals(
            $expectedFieldValue->data,
            $fieldValue->data
        );

        ClockMock::withClockMock(false);
    }

    public function xmlToFieldValueProvider(): array
    {
        $pathToImg = __DIR__ . '/../_fixtures/ibexa_fav.png';
        $dir = __DIR__ . '/../_fixtures';

        return [
            'with_additional_data' => [
<<< XML
<?xml version="1.0" encoding="utf-8"?>
<ezimage serial_number="1" is_valid="1" filename="ibexa_fav.png"
    suffix="png" basename="ibexa_fav" dirpath="{$dir}" url="{$pathToImg}"
    original_filename="ibexa_fav.png" mime_type="image/png" width="100"
    height="200" alternative_text="test" alias_key="1293033771" timestamp="{timestampToReplace}">
  <original attribute_id="1" attribute_version="1" attribute_language="eng-GB"/>
  <information Height="200" Width="100" IsColor="1"/>
  <additional_data>
    <attribute key="focalPointX">50</attribute>
    <attribute key="focalPointY">100</attribute>
    <attribute key="author">John Smith</attribute>
  </additional_data>
</ezimage>
XML,
                new FieldValue([
                    'data' => [
                        'width' => '100',
                        'height' => '200',
                        'alternativeText' => 'test',
                        'mime' => 'image/png',
                        'id' => 1,
                        'fileName' => 'ibexa_fav.png',
                        'additionalData' => [
                            'focalPointX' => 50,
                            'focalPointY' => 100,
                            'author' => 'John Smith',
                        ],
                    ],
                ]),
            ],
            'without_additional_data_stored' => [
<<< XML
<?xml version="1.0" encoding="utf-8"?>
<ezimage serial_number="1" is_valid="1" filename="ibexa_fav.png"
    suffix="png" basename="ibexa_fav" dirpath="{$dir}" url="{$pathToImg}"
    original_filename="ibexa_fav.png" mime_type="image/png" width="100"
    height="200" alternative_text="test" alias_key="1293033771" timestamp="{timestampToReplace}">
  <original attribute_id="1" attribute_version="1" attribute_language="eng-GB"/>
  <information Height="200" Width="100" IsColor="1"/>
</ezimage>
XML,
                new FieldValue([
                    'data' => [
                        'width' => '100',
                        'height' => '200',
                        'alternativeText' => 'test',
                        'mime' => 'image/png',
                        'id' => 1,
                        'fileName' => 'ibexa_fav.png',
                        'additionalData' => [],
                    ],
                ]),
            ],
        ];
    }
}
