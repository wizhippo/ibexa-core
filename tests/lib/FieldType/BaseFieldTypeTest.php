<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Tests\Core\FieldType;

use Ibexa\Contracts\Core\FieldType\FieldType;
use Ibexa\Contracts\Core\FieldType\ValidationError;
use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition as APIFieldDefinition;
use PHPUnit\Framework\TestCase;

abstract class BaseFieldTypeTest extends TestCase
{
    /**
     * Generic cache for the getFieldTypeUnderTest() method.
     *
     * @var \Ibexa\Contracts\Core\FieldType\FieldType
     */
    private $fieldTypeUnderTest;

    /**
     * Returns the identifier of the field type under test.
     *
     * @return string
     */
    abstract protected function provideFieldTypeIdentifier();

    /**
     * Returns the field type under test.
     *
     * This method is used by all test cases to retrieve the field type under
     * test. Just create the FieldType instance using mocks from the provided
     * get*Mock() methods and/or custom get*Mock() implementations. You MUST
     * NOT take care for test case wide caching of the field type, just return
     * a new instance from this method!
     *
     * @return \Ibexa\Core\FieldType\FieldType
     */
    abstract protected function createFieldTypeUnderTest();

    /**
     * Returns the validator configuration schema expected from the field type.
     *
     * @return array
     */
    abstract protected function getValidatorConfigurationSchemaExpectation();

    /**
     * Returns the settings schema expected from the field type.
     *
     * @return array
     */
    abstract protected function getSettingsSchemaExpectation();

    /**
     * Returns the empty value expected from the field type.
     *
     * @return mixed
     */
    abstract protected function getEmptyValueExpectation();

    /**
     * Data provider for invalid input to acceptValue().
     *
     * Returns an array of data provider sets with 2 arguments: 1. The invalid
     * input to acceptValue(), 2. The expected exception type as a string. For
     * example:
     *
     * <code>
     *  return array(
     *      array(
     *          new \stdClass(),
     *          InvalidArgumentException::class,
     *      ),
     *      array(
     *          array(),
     *          InvalidArgumentException::class,
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    abstract public function provideInvalidInputForAcceptValue();

    /**
     * Data provider for valid input to acceptValue().
     *
     * Returns an array of data provider sets with 2 arguments: 1. The valid
     * input to acceptValue(), 2. The expected return value from acceptValue().
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          null,
     *          null
     *      ),
     *      array(
     *          __FILE__,
     *          new BinaryFileValue( array(
     *              'path' => __FILE__,
     *              'fileName' => basename( __FILE__ ),
     *              'fileSize' => filesize( __FILE__ ),
     *              'downloadCount' => 0,
     *              'mimeType' => 'text/plain',
     *          ) )
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    abstract public function provideValidInputForAcceptValue();

    /**
     * Provide input for the toHash() method.
     *
     * Returns an array of data provider sets with 2 arguments: 1. The valid
     * input to toHash(), 2. The expected return value from toHash().
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          null,
     *          null
     *      ),
     *      array(
     *          new BinaryFileValue( array(
     *              'path' => 'some/file/here',
     *              'fileName' => 'sindelfingen.jpg',
     *              'fileSize' => 2342,
     *              'downloadCount' => 0,
     *              'mimeType' => 'image/jpeg',
     *          ) ),
     *          array(
     *              'path' => 'some/file/here',
     *              'fileName' => 'sindelfingen.jpg',
     *              'fileSize' => 2342,
     *              'downloadCount' => 0,
     *              'mimeType' => 'image/jpeg',
     *          )
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    abstract public function provideInputForToHash();

    /**
     * Provide input to fromHash() method.
     *
     * Returns an array of data provider sets with 2 arguments: 1. The valid
     * input to fromHash(), 2. The expected return value from fromHash().
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          null,
     *          null
     *      ),
     *      array(
     *          array(
     *              'path' => 'some/file/here',
     *              'fileName' => 'sindelfingen.jpg',
     *              'fileSize' => 2342,
     *              'downloadCount' => 0,
     *              'mimeType' => 'image/jpeg',
     *          ),
     *          new BinaryFileValue( array(
     *              'path' => 'some/file/here',
     *              'fileName' => 'sindelfingen.jpg',
     *              'fileSize' => 2342,
     *              'downloadCount' => 0,
     *              'mimeType' => 'image/jpeg',
     *          ) )
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    abstract public function provideInputForFromHash();

    /**
     * Provides data for the getName() test.
     *
     * @return array
     */
    abstract public function provideDataForGetName(): array;

    /**
     * Provide data sets with field settings which are considered valid by the
     * {@link validateFieldSettings()} method.
     *
     * ATTENTION: This is a default implementation, which must be overwritten
     * if a FieldType supports field settings!
     *
     * Returns an array of data provider sets with a single argument: A valid
     * set of field settings.
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          array(),
     *      ),
     *      array(
     *          array( 'rows' => 2 )
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideValidFieldSettings()
    {
        return [
            [
                [],
            ],
        ];
    }

    /**
     * Provide data sets with field settings which are considered invalid by the
     * {@link validateFieldSettings()} method. The method must return a
     * non-empty array of validation error when receiving such field settings.
     *
     * ATTENTION: This is a default implementation, which must be overwritten
     * if a FieldType supports field settings!
     *
     * Returns an array of data provider sets with a single argument: A valid
     * set of field settings.
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          true,
     *      ),
     *      array(
     *          array( 'nonExistentKey' => 2 )
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideInValidFieldSettings()
    {
        return [
            [
                ['nonempty'],
            ],
        ];
    }

    /**
     * Provide data sets with validator configurations which are considered
     * valid by the {@link validateValidatorConfiguration()} method.
     *
     * ATTENTION: This is a default implementation, which must be overwritten
     * if a FieldType supports validators!
     *
     * Returns an array of data provider sets with a single argument: A valid
     * set of validator configurations.
     *
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          array(),
     *      ),
     *      array(
     *          array(
     *              'IntegerValueValidator' => array(
     *                  'minIntegerValue' => 0,
     *                  'maxIntegerValue' => 23,
     *              )
     *          )
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideValidValidatorConfiguration()
    {
        return [
            [
                [],
            ],
        ];
    }

    /**
     * Provide data sets with validator configurations which are considered
     * invalid by the {@link validateValidatorConfiguration()} method. The
     * method must return a non-empty array of valiation errors when receiving
     * one of the provided values.
     *
     * ATTENTION: This is a default implementation, which must be overwritten
     * if a FieldType supports validators!
     *
     * Returns an array of data provider sets with a single argument: A valid
     * set of validator configurations.
     *
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          array(
     *              'NonExistentValidator' => array(),
     *          ),
     *      ),
     *      array(
     *          array(
     *              // Typos
     *              'InTEgervALUeVALIdator' => array(
     *                  'minIntegerValue' => 0,
     *                  'maxIntegerValue' => 23,
     *              )
     *          )
     *      ),
     *      array(
     *          array(
     *              'IntegerValueValidator' => array(
     *                  // Incorrect value types
     *                  'minIntegerValue' => true,
     *                  'maxIntegerValue' => false,
     *              )
     *          )
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideInvalidValidatorConfiguration()
    {
        return [
            [
                [
                    'NonExistentValidator' => [],
                ],
            ],
        ];
    }

    /**
     * Provides data sets with validator configuration and/or field settings and
     * field value which are considered valid by the {@link validate()} method.
     *
     * ATTENTION: This is a default implementation, which must be overwritten if
     * a FieldType supports validation!
     *
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          array(
     *              "validatorConfiguration" => array(
     *                  "StringLengthValidator" => array(
     *                      "minStringLength" => 2,
     *                      "maxStringLength" => 10,
     *                  ),
     *              ),
     *          ),
     *          new TextLineValue( "lalalala" ),
     *      ),
     *      array(
     *          array(
     *              "fieldSettings" => array(
     *                  'isMultiple' => true
     *              ),
     *          ),
     *          new CountryValue(
     *              array(
     *                  "BE" => array(
     *                      "Name" => "Belgium",
     *                      "Alpha2" => "BE",
     *                      "Alpha3" => "BEL",
     *                      "IDC" => 32,
     *                  ),
     *              ),
     *          ),
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideValidDataForValidate()
    {
        return [
            [
                [],
                $this->createMock(SPIValue::class),
            ],
        ];
    }

    /**
     * Provides data sets with validator configuration and/or field settings,
     * field value and corresponding validation errors returned by
     * the {@link validate()} method.
     *
     * ATTENTION: This is a default implementation, which must be overwritten
     * if a FieldType supports validation!
     *
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          array(
     *              "validatorConfiguration" => array(
     *                  "IntegerValueValidator" => array(
     *                      "minIntegerValue" => 5,
     *                      "maxIntegerValue" => 10
     *                  ),
     *              ),
     *          ),
     *          new IntegerValue( 3 ),
     *          array(
     *              new ValidationError(
     *                  "The value can not be lower than %size%.",
     *                  null,
     *                  array(
     *                      "%size%" => 5
     *                  ),
     *              ),
     *          ),
     *      ),
     *      array(
     *          array(
     *              "fieldSettings" => array(
     *                  "isMultiple" => false
     *              ),
     *          ),
     *          new CountryValue(
     *              "BE" => array(
     *                  "Name" => "Belgium",
     *                  "Alpha2" => "BE",
     *                  "Alpha3" => "BEL",
     *                  "IDC" => 32,
     *              ),
     *              "FR" => array(
     *                  "Name" => "France",
     *                  "Alpha2" => "FR",
     *                  "Alpha3" => "FRA",
     *                  "IDC" => 33,
     *              ),
     *          )
     *      ),
     *      array(
     *          new ValidationError(
     *              "Field definition does not allow multiple countries to be selected."
     *          ),
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideInvalidDataForValidate()
    {
        return [
            [
                [],
                $this->createMock(SPIValue::class),
                [],
            ],
        ];
    }

    /**
     * Retrieves a test wide cached version of the field type under test.
     *
     * Uses {@link createFieldTypeUnderTest()} to create the instance
     * initially.
     *
     * @return \Ibexa\Contracts\Core\FieldType\FieldType
     */
    protected function getFieldTypeUnderTest()
    {
        if (!isset($this->fieldTypeUnderTest)) {
            $this->fieldTypeUnderTest = $this->createFieldTypeUnderTest();
        }

        return $this->fieldTypeUnderTest;
    }

    public function testGetFieldTypeIdentifier()
    {
        self::assertSame(
            $this->provideFieldTypeIdentifier(),
            $this->getFieldTypeUnderTest()->getFieldTypeIdentifier()
        );
    }

    /**
     * @dataProvider provideDataForGetName
     */
    public function testGetName(
        SPIValue $value,
        string $expected,
        array $fieldSettings = [],
        string $languageCode = 'en_GB'
    ): void {
        $fieldDefinitionMock = $this->getFieldDefinitionMock($fieldSettings);

        self::assertSame(
            $expected,
            $this->getFieldTypeUnderTest()->getName($value, $fieldDefinitionMock, $languageCode)
        );
    }

    public function testValidatorConfigurationSchema()
    {
        $fieldType = $this->getFieldTypeUnderTest();

        self::assertSame(
            $this->getValidatorConfigurationSchemaExpectation(),
            $fieldType->getValidatorConfigurationSchema(),
            'Validator configuration schema not returned correctly.'
        );
    }

    public function testSettingsSchema()
    {
        $fieldType = $this->getFieldTypeUnderTest();

        self::assertSame(
            $this->getSettingsSchemaExpectation(),
            $fieldType->getSettingsSchema(),
            'Settings schema not returned correctly.'
        );
    }

    public function testEmptyValue()
    {
        $fieldType = $this->getFieldTypeUnderTest();

        self::assertEquals(
            $this->getEmptyValueExpectation(),
            $fieldType->getEmptyValue()
        );
    }

    /**
     * @param mixed $inputValue
     * @param mixed $expectedOutputValue
     *
     * @dataProvider provideValidInputForAcceptValue
     */
    public function testAcceptValue($inputValue, $expectedOutputValue)
    {
        $fieldType = $this->getFieldTypeUnderTest();

        $outputValue = $fieldType->acceptValue($inputValue);

        self::assertEquals(
            $expectedOutputValue,
            $outputValue,
            'acceptValue() did not convert properly.'
        );
    }

    /**
     * Tests that default empty value is unchanged by acceptValue() method.
     */
    public function testAcceptGetEmptyValue()
    {
        $fieldType = $this->getFieldTypeUnderTest();
        $emptyValue = $fieldType->getEmptyValue();

        $acceptedEmptyValue = $fieldType->acceptValue($emptyValue);

        self::assertEquals(
            $emptyValue,
            $acceptedEmptyValue,
            'acceptValue() did not convert properly.'
        );
    }

    /**
     * @param mixed $inputValue
     *
     * @dataProvider provideInvalidInputForAcceptValue
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testAcceptValueFailsOnInvalidValues(
        $inputValue,
        string $expectedException
    ): void {
        $fieldType = $this->getFieldTypeUnderTest();

        $this->expectException($expectedException);
        $fieldType->acceptValue($inputValue);
    }

    /**
     * @param mixed $inputValue
     * @param array $expectedResult
     *
     * @dataProvider provideInputForToHash
     */
    public function testToHash($inputValue, $expectedResult)
    {
        $fieldType = $this->getFieldTypeUnderTest();

        $actualResult = $fieldType->toHash($inputValue);

        $this->assertIsValidHashValue($actualResult);

        if (is_object($expectedResult) || is_array($expectedResult)) {
            self::assertEquals(
                $expectedResult,
                $actualResult,
                'toHash() method did not create expected result.'
            );
        } else {
            self::assertSame(
                $expectedResult,
                $actualResult,
                'toHash() method did not create expected result.'
            );
        }
    }

    /**
     * @param mixed $inputValue
     * @param array $expectedResult
     *
     * @dataProvider provideInputForFromHash
     */
    public function testFromHash($inputHash, $expectedResult)
    {
        $this->assertIsValidHashValue($inputHash);

        $fieldType = $this->getFieldTypeUnderTest();

        $actualResult = $fieldType->fromHash($inputHash);

        if (is_object($expectedResult) || is_array($expectedResult)) {
            self::assertEquals(
                $expectedResult,
                $actualResult,
                'fromHash() method did not create expected result.'
            );
        } else {
            self::assertSame(
                $expectedResult,
                $actualResult,
                'fromHash() method did not create expected result.'
            );
        }
    }

    public function testEmptyValueIsEmpty()
    {
        $fieldType = $this->getFieldTypeUnderTest();

        self::assertTrue(
            $fieldType->isEmptyValue($fieldType->getEmptyValue())
        );
    }

    /**
     * @param mixed $inputSettings
     *
     * @dataProvider provideValidFieldSettings
     */
    public function testValidateFieldSettingsValid($inputSettings)
    {
        $fieldType = $this->getFieldTypeUnderTest();

        $validationResult = $fieldType->validateFieldSettings($inputSettings);

        self::assertIsArray(
            $validationResult,
            'The method validateFieldSettings() must return an array.'
        );
        self::assertEquals(
            [],
            $validationResult,
            'validateFieldSettings() did not consider the input settings valid.'
        );
    }

    /**
     * @param mixed $inputSettings
     *
     * @dataProvider provideInvalidFieldSettings
     */
    public function testValidateFieldSettingsInvalid($inputSettings)
    {
        $fieldType = $this->getFieldTypeUnderTest();

        $validationResult = $fieldType->validateFieldSettings($inputSettings);

        self::assertIsArray(
            $validationResult,
            'The method validateFieldSettings() must return an array.'
        );

        self::assertNotEquals(
            [],
            $validationResult,
            'validateFieldSettings() did consider the input settings valid, which should be invalid.'
        );

        foreach ($validationResult as $actualResultElement) {
            self::assertInstanceOf(
                ValidationError::class,
                $actualResultElement,
                'Validation result of incorrect type.'
            );
        }
    }

    /**
     * @param mixed $inputConfiguration
     *
     * @dataProvider provideValidValidatorConfiguration
     */
    public function testValidateValidatorConfigurationValid($inputConfiguration)
    {
        $fieldType = $this->getFieldTypeUnderTest();

        $validationResult = $fieldType->validateValidatorConfiguration($inputConfiguration);

        self::assertIsArray(
            $validationResult,
            'The method validateValidatorConfiguration() must return an array.'
        );
        self::assertEquals(
            [],
            $validationResult,
            'validateValidatorConfiguration() did not consider the input configuration valid.'
        );
    }

    /**
     * @param mixed $inputConfiguration
     *
     * @dataProvider provideInvalidValidatorConfiguration
     */
    public function testValidateValidatorConfigurationInvalid($inputConfiguration)
    {
        $fieldType = $this->getFieldTypeUnderTest();

        $validationResult = $fieldType->validateValidatorConfiguration($inputConfiguration);

        self::assertIsArray(
            $validationResult,
            'The method validateValidatorConfiguration() must return an array.'
        );

        self::assertNotEquals(
            [],
            $validationResult,
            'validateValidatorConfiguration() did consider the input settings valid, which should be invalid.'
        );

        foreach ($validationResult as $actualResultElement) {
            self::assertInstanceOf(
                ValidationError::class,
                $actualResultElement,
                'Validation result of incorrect type.'
            );
        }
    }

    /**
     * @param mixed $inputConfiguration
     *
     * @dataProvider provideValidFieldSettings
     */
    public function testFieldSettingsToHash($inputSettings)
    {
        $fieldType = $this->getFieldTypeUnderTest();

        $hash = $fieldType->fieldSettingsToHash($inputSettings);

        $this->assertIsValidHashValue($hash);
    }

    /**
     * @param mixed $inputConfiguration
     *
     * @dataProvider provideValidValidatorConfiguration
     */
    public function testValidatorConfigurationToHash($inputConfiguration)
    {
        $fieldType = $this->getFieldTypeUnderTest();

        $hash = $fieldType->validatorConfigurationToHash($inputConfiguration);

        $this->assertIsValidHashValue($hash);
    }

    /**
     * @param mixed $inputConfiguration
     *
     * @dataProvider provideValidFieldSettings
     */
    public function testFieldSettingsFromHash($inputSettings)
    {
        $fieldType = $this->getFieldTypeUnderTest();

        $hash = $fieldType->fieldSettingsToHash($inputSettings);
        $restoredSettings = $fieldType->fieldSettingsFromHash($hash);

        self::assertEquals($inputSettings, $restoredSettings);
    }

    /**
     * @param mixed $inputConfiguration
     *
     * @dataProvider provideValidValidatorConfiguration
     */
    public function testValidatorConfigurationFromHash($inputConfiguration)
    {
        $fieldType = $this->getFieldTypeUnderTest();

        $hash = $fieldType->validatorConfigurationToHash($inputConfiguration);
        $restoredConfiguration = $fieldType->validatorConfigurationFromHash($hash);

        self::assertEquals($inputConfiguration, $restoredConfiguration);
    }

    /**
     * Asserts that the given $actualHash complies to the rules for hashes.
     *
     * @param mixed $actualHash
     * @param array $keyChain
     */
    protected function assertIsValidHashValue($actualHash, $keyChain = [])
    {
        switch ($actualHashType = gettype($actualHash)) {
            case 'boolean':
            case 'integer':
            case 'double':
            case 'string':
            case 'NULL':
                // All valid, just return
                return;

            case 'array':
                foreach ($actualHash as $key => $childHash) {
                    $this->assertIsValidHashValue(
                        $childHash,
                        array_merge($keyChain, [$key])
                    );
                }

                return;

            case 'resource':
            case 'object':
                self::fail(
                    sprintf(
                        'Value for $hash[%s] is of invalid type "%s".',
                        implode('][', $keyChain),
                        $actualHashType
                    )
                );
        }
    }

    /**
     * @dataProvider provideValidDataForValidate
     */
    public function testValidateValid($fieldDefinitionData, $value)
    {
        $validationErrors = $this->doValidate($fieldDefinitionData, $value);

        self::assertIsArray($validationErrors);
        self::assertEmpty($validationErrors, "Got value:\n" . var_export($validationErrors, true));
    }

    /**
     * @dataProvider provideInvalidDataForValidate
     */
    public function testValidateInvalid($fieldDefinitionData, $value, $errors)
    {
        $validationErrors = $this->doValidate($fieldDefinitionData, $value);

        self::assertIsArray($validationErrors);
        self::assertEquals($errors, $validationErrors);
    }

    protected function doValidate($fieldDefinitionData, $value)
    {
        $fieldType = $this->getFieldTypeUnderTest();

        /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition|\PHPUnit\Framework\MockObject\MockObject $fieldDefinitionMock */
        $fieldDefinitionMock = $this->createMock(APIFieldDefinition::class);

        foreach ($fieldDefinitionData as $method => $data) {
            if ($method === 'validatorConfiguration') {
                $fieldDefinitionMock
                    ->method('getValidatorConfiguration')
                    ->willReturn($data);
            }

            if ($method === 'fieldSettings') {
                $fieldDefinitionMock
                    ->method('getFieldSettings')
                    ->willReturn($data);
            }
        }

        return $fieldType->validate($fieldDefinitionMock, $value);
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getFieldDefinitionMock(array $fieldSettings)
    {
        /** @var |\PHPUnit\Framework\MockObject\MockObject $fieldDefinitionMock */
        $fieldDefinitionMock = $this->createMock(APIFieldDefinition::class);
        $fieldDefinitionMock
            ->method('getFieldSettings')
            ->willReturn($fieldSettings);

        return $fieldDefinitionMock;
    }

    // @todo: More test methods …
}
