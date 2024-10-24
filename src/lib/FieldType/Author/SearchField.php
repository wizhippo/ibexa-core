<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Core\FieldType\Author;

use Ibexa\Contracts\Core\FieldType\Indexable;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Contracts\Core\Search;
use Ibexa\Contracts\Core\Search\FieldType\MultipleIdentifierField;

/**
 * Indexable definition for Author field type.
 */
class SearchField implements Indexable
{
    public function getIndexData(Field $field, FieldDefinition $fieldDefinition)
    {
        $name = [];
        $id = [];
        $email = [];
        $aggregationValues = [];

        foreach ($field->value->data as $author) {
            $name[] = $author['name'];
            $id[] = $author['id'];
            $email[] = $author['email'];

            $aggregationValues[] = json_encode([
                'name' => $author['name'],
                'email' => $author['email'],
            ]);
        }

        return [
            new Search\Field(
                'name',
                $name,
                new Search\FieldType\MultipleStringField()
            ),
            new Search\Field(
                'id',
                $id,
                new Search\FieldType\MultipleIntegerField()
            ),
            new Search\Field(
                'email',
                $email,
                new Search\FieldType\MultipleStringField()
            ),
            new Search\Field(
                'count',
                count($field->value->data),
                new Search\FieldType\IntegerField()
            ),
            new Search\Field(
                'aggregation_value',
                $aggregationValues,
                new MultipleIdentifierField(['raw' => true]),
            ),
            new Search\Field(
                'sort_value',
                implode('-', $name),
                new Search\FieldType\StringField()
            ),
            new Search\Field(
                'fulltext',
                $name,
                new Search\FieldType\FullTextField()
            ),
        ];
    }

    public function getIndexDefinition()
    {
        return [
            'name' => new Search\FieldType\MultipleStringField(),
            'id' => new Search\FieldType\MultipleIntegerField(),
            'email' => new Search\FieldType\MultipleStringField(),
            'count' => new Search\FieldType\IntegerField(),
            'aggregation_value' => new MultipleIdentifierField(['raw' => true]),
            'sort_value' => new Search\FieldType\StringField(),
        ];
    }

    /**
     * Get name of the default field to be used for matching.
     *
     * As field types can index multiple fields (see MapLocation field type's
     * implementation of this interface), this method is used to define default
     * field for matching. Default field is typically used by Field criterion.
     *
     * @return string
     */
    public function getDefaultMatchField()
    {
        return 'name';
    }

    /**
     * Get name of the default field to be used for sorting.
     *
     * As field types can index multiple fields (see MapLocation field type's
     * implementation of this interface), this method is used to define default
     * field for sorting. Default field is typically used by Field sort clause.
     *
     * @return string
     */
    public function getDefaultSortField()
    {
        return 'sort_value';
    }
}
