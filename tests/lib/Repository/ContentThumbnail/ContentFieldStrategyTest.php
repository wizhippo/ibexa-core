<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Repository\ContentThumbnail;

use ArrayIterator;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\Field\FieldTypeBasedThumbnailStrategy;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\Thumbnail;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Strategy\ContentThumbnail\Field\ContentFieldStrategy;
use PHPUnit\Framework\TestCase;

class ContentFieldStrategyTest extends TestCase
{
    private function getFieldTypeBasedThumbnailStrategy(string $fieldTypeIdentifier): FieldTypeBasedThumbnailStrategy
    {
        return new class($fieldTypeIdentifier) implements FieldTypeBasedThumbnailStrategy {
            /** @var string */
            private $fieldTypeIdentifier;

            public function __construct(string $fieldTypeIdentifier)
            {
                $this->fieldTypeIdentifier = $fieldTypeIdentifier;
            }

            public function getFieldTypeIdentifier(): string
            {
                return $this->fieldTypeIdentifier;
            }

            public function getThumbnail(Field $field, ?VersionInfo $versionInfo = null): ?Thumbnail
            {
                return new Thumbnail([
                    'resource' => $field->value,
                ]);
            }
        };
    }

    public function testHasStrategy(): void
    {
        $contentFieldStrategy = new ContentFieldStrategy(new ArrayIterator([
            $this->getFieldTypeBasedThumbnailStrategy('example'),
        ]));

        self::assertTrue($contentFieldStrategy->hasStrategy('example'));
        self::assertFalse($contentFieldStrategy->hasStrategy('something_else'));
    }

    public function testAddStrategy(): void
    {
        $contentFieldStrategy = new ContentFieldStrategy(new ArrayIterator());

        self::assertFalse($contentFieldStrategy->hasStrategy('example'));

        $contentFieldStrategy->addStrategy('example', $this->getFieldTypeBasedThumbnailStrategy('example'));

        self::assertTrue($contentFieldStrategy->hasStrategy('example'));
    }

    public function testSetStrategies(): void
    {
        $contentFieldStrategy = new ContentFieldStrategy(new ArrayIterator([
            $this->getFieldTypeBasedThumbnailStrategy('previous'),
        ]));

        self::assertTrue($contentFieldStrategy->hasStrategy('previous'));

        $contentFieldStrategy->setStrategies([
            $this->getFieldTypeBasedThumbnailStrategy('new-example-1'),
            $this->getFieldTypeBasedThumbnailStrategy('new-example-2'),
        ]);

        self::assertFalse($contentFieldStrategy->hasStrategy('previous'));
        self::assertTrue($contentFieldStrategy->hasStrategy('new-example-1'));
        self::assertTrue($contentFieldStrategy->hasStrategy('new-example-2'));
    }

    public function testGetThumbnailFound(): void
    {
        $contentFieldStrategy = new ContentFieldStrategy(new ArrayIterator([
            $this->getFieldTypeBasedThumbnailStrategy('example'),
        ]));

        $field = new Field([
            'fieldTypeIdentifier' => 'example',
            'value' => 'example-value',
        ]);

        $thumbnail = $contentFieldStrategy->getThumbnail($field);

        self::assertInstanceOf(Thumbnail::class, $thumbnail);
        self::assertEquals('example-value', $thumbnail->resource);
    }

    public function testGetThumbnailNotFound(): void
    {
        $contentFieldStrategy = new ContentFieldStrategy(new ArrayIterator([
            $this->getFieldTypeBasedThumbnailStrategy('something-else'),
        ]));

        $field = new Field([
            'fieldTypeIdentifier' => 'example',
            'value' => 'example-value',
        ]);

        $this->expectException(NotFoundException::class);

        $contentFieldStrategy->getThumbnail($field);
    }
}
