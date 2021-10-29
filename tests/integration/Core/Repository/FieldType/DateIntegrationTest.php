<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\API\Repository\Tests\FieldType;

use eZ\Publish\Core\FieldType\Date\Value as DateValue;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\FieldType\Date\Type;
use DateTime;

/**
 * Integration test for use field type.
 *
 * @group integration
 * @group field-type
 */
class DateIntegrationTest extends SearchBaseIntegrationTest
{
    /**
     * Get name of tested field type.
     *
     * @return string
     */
    public function getTypeName()
    {
        return 'ezdate';
    }

    /**
     * {@inheritdoc}
     */
    protected function supportsLikeWildcard($value)
    {
        parent::supportsLikeWildcard($value);

        return false;
    }

    /**
     * Get expected settings schema.
     *
     * @return array
     */
    public function getSettingsSchema()
    {
        return [
            'defaultType' => [
                'type' => 'choice',
                'default' => Type::DEFAULT_EMPTY,
            ],
        ];
    }

    /**
     * Get a valid $fieldSettings value.
     *
     * @return mixed
     */
    public function getValidFieldSettings()
    {
        return [
            'defaultType' => Type::DEFAULT_EMPTY,
        ];
    }

    /**
     * Get $fieldSettings value not accepted by the field type.
     *
     * @return mixed
     */
    public function getInvalidFieldSettings()
    {
        return [
            'somethingUnknown' => 0,
        ];
    }

    /**
     * Get expected validator schema.
     *
     * @return array
     */
    public function getValidatorSchema()
    {
        return [];
    }

    /**
     * Get a valid $validatorConfiguration.
     *
     * @return mixed
     */
    public function getValidValidatorConfiguration()
    {
        return [];
    }

    /**
     * Get $validatorConfiguration not accepted by the field type.
     *
     * @return mixed
     */
    public function getInvalidValidatorConfiguration()
    {
        return [
            'unknown' => ['value' => 42],
        ];
    }

    /**
     * Get initial field data for valid object creation.
     *
     * @return mixed
     */
    public function getValidCreationFieldData()
    {
        $dateTime = $this->getValueOneDate();

        return DateValue::fromTimestamp($dateTime->getTimestamp());
    }

    /**
     * Get name generated by the given field type (via fieldType->getName()).
     *
     * @return string
     */
    public function getFieldName()
    {
        return 'Friday 02 January 1970';
    }

    /**
     * Asserts that the field data was loaded correctly.
     *
     * Asserts that the data provided by {@link getValidCreationFieldData()}
     * was stored and loaded correctly.
     *
     * @param Field $field
     */
    public function assertFieldDataLoadedCorrect(Field $field)
    {
        $this->assertInstanceOf(
            'eZ\\Publish\\Core\\FieldType\\Date\\Value',
            $field->value
        );

        $dateTime = $this->getValueOneDate();

        $expectedData = [
            'date' => $dateTime,
        ];

        $this->assertPropertiesCorrect(
            $expectedData,
            $field->value
        );
    }

    /**
     * Get field data which will result in errors during creation.
     *
     * This is a PHPUnit data provider.
     *
     * The returned records must contain of an error producing data value and
     * the expected exception class (from the API or SPI, not implementation
     * specific!) as the second element. For example:
     *
     * <code>
     * array(
     *      array(
     *          new DoomedValue( true ),
     *          'eZ\\Publish\\API\\Repository\\Exceptions\\ContentValidationException'
     *      ),
     *      // ...
     * );
     * </code>
     *
     * @return array[]
     */
    public function provideInvalidCreationFieldData()
    {
        return [
            [
                'Some unknown date format',
                'eZ\\Publish\\API\\Repository\\Exceptions\\InvalidArgumentException',
            ],
        ];
    }

    /**
     * Get valid field data for updating content.
     *
     * @return mixed
     */
    public function getValidUpdateFieldData()
    {
        $dateTime = $this->getValueOneDate();

        return DateValue::fromTimestamp($dateTime->getTimestamp());
    }

    /**
     * Asserts the field data was loaded correctly.
     *
     * Asserts that the data provided by {@link getValidUpdateFieldData()}
     * was stored and loaded correctly.
     *
     * @param Field $field
     */
    public function assertUpdatedFieldDataLoadedCorrect(Field $field)
    {
        $this->assertInstanceOf(
            'eZ\\Publish\\Core\\FieldType\\Date\\Value',
            $field->value
        );

        $dateTime = $this->getValueOneDate();

        $expectedData = [
            'date' => $dateTime,
        ];
        $this->assertPropertiesCorrect(
            $expectedData,
            $field->value
        );
    }

    /**
     * Get field data which will result in errors during update.
     *
     * This is a PHPUnit data provider.
     *
     * The returned records must contain of an error producing data value and
     * the expected exception class (from the API or SPI, not implementation
     * specific!) as the second element. For example:
     *
     * <code>
     * array(
     *      array(
     *          new DoomedValue( true ),
     *          'eZ\\Publish\\API\\Repository\\Exceptions\\ContentValidationException'
     *      ),
     *      // ...
     * );
     * </code>
     *
     * @return array[]
     */
    public function provideInvalidUpdateFieldData()
    {
        return $this->provideInvalidCreationFieldData();
    }

    /**
     * Asserts the the field data was loaded correctly.
     *
     * Asserts that the data provided by {@link getValidCreationFieldData()}
     * was copied and loaded correctly.
     *
     * @param Field $field
     */
    public function assertCopiedFieldDataLoadedCorrectly(Field $field)
    {
        $this->assertFieldDataLoadedCorrect($field);
    }

    /**
     * Get data to test to hash method.
     *
     * This is a PHPUnit data provider
     *
     * The returned records must have the the original value assigned to the
     * first index and the expected hash result to the second. For example:
     *
     * <code>
     * array(
     *      array(
     *          new MyValue( true ),
     *          array( 'myValue' => true ),
     *      ),
     *      // ...
     * );
     * </code>
     *
     * @return array
     */
    public function provideToHashData()
    {
        $timestamp = 186400;
        $dateTime = new DateTime();
        $dateTime->setTimestamp($timestamp);

        return [
            [
                DateValue::fromTimestamp($timestamp),
                [
                    'timestamp' => $dateTime->setTime(0, 0, 0)->getTimestamp(),
                    'rfc850' => $dateTime->format(DateTime::RFC850),
                ],
            ],
        ];
    }

    /**
     * Get hashes and their respective converted values.
     *
     * This is a PHPUnit data provider
     *
     * The returned records must have the the input hash assigned to the
     * first index and the expected value result to the second. For example:
     *
     * <code>
     * array(
     *      array(
     *          array( 'myValue' => true ),
     *          new MyValue( true ),
     *      ),
     *      // ...
     * );
     * </code>
     *
     * @return array
     */
    public function provideFromHashData()
    {
        $timestamp = 123456;

        $dateTime = new DateTime();
        $dateTime->setTimestamp($timestamp)->setTime(0, 0, 0);

        return [
            [
                [
                    'timestamp' => $dateTime->getTimestamp(),
                    'rfc850' => ($rfc850 = $dateTime->format(DateTime::RFC850)),
                ],
                DateValue::fromString($rfc850),
            ],
            [
                [
                    'timestamp' => $dateTime->getTimestamp(),
                    'rfc850' => null,
                ],
                DateValue::fromTimestamp($dateTime->getTimestamp()),
            ],
        ];
    }

    public function providerForTestIsEmptyValue()
    {
        return [
            [new DateValue()],
        ];
    }

    public function providerForTestIsNotEmptyValue()
    {
        return [
            [
                $this->getValidCreationFieldData(),
            ],
        ];
    }

    protected function getValidSearchValueOne()
    {
        $dateTime = $this->getValueOneDate();

        return $dateTime->getTimestamp();
    }

    protected function getValidSearchValueTwo()
    {
        $dateTime = new DateTime('1970-01-03');

        return $dateTime->getTimestamp();
    }

    protected function getSearchTargetValueOne()
    {
        // Handling Legacy Search Engine, which stores Date value as timestamp
        if (ltrim(get_class($this->getSetupFactory()), '\\') === 'eZ\Publish\API\Repository\Tests\SetupFactory\Legacy') {
            return $this->getValidSearchValueOne();
        }

        return '1970-01-02T00:00:00Z';
    }

    protected function getSearchTargetValueTwo()
    {
        // Handling Legacy Search Engine, which stores Date value as timestamp
        if (ltrim(get_class($this->getSetupFactory()), '\\') === 'eZ\Publish\API\Repository\Tests\SetupFactory\Legacy') {
            return $this->getValidSearchValueTwo();
        }

        return '1970-01-03T00:00:00Z';
    }

    protected function getValueOneDate(): DateTime
    {
        return new DateTime('1970-01-02');
    }
}