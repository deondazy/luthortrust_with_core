<?php

use Pest\Expectation;
use Denosys\Core\Support\DataTransferObject;

class Subclass extends DataTransferObject
{
    public function __construct(
        public readonly string $property1,
        public readonly ?string $property2,
        public readonly string $property3 = 'default',
    ) {
    }
}

it('creates a new instance of a subclass with valid input', function () {
    // Arrange
    $values = ['property1' => 'value1', 'property2' => 'value2'];

    // Act
    $instance = Subclass::createFromArray($values);

    // Assert
    expect($instance)->toBeInstanceOf(Subclass::class);
    expect($instance->property1)->toBe('value1');
    expect($instance->property2)->toBe('value2');
});

it('returns an array with all properties', function () {
    // Arrange
    $instance = new Subclass(
        'value1',
        'value2',
        'value3',
    );

    // Act
    $result = $instance->toArray();

    // Assert
    expect($result)->toBe(['property1' => 'value1', 'property2' => 'value2', 'property3' => 'value3']);
});

it('sets missing parameter to null if no default value', function () {
    // Arrange
    $values = ['property1' => 'value1'];

    // Act
    $instance = Subclass::createFromArray($values);

    // Assert
    expect($instance->property1)->toBe('value1');
    expect($instance->property2)->toBeNull();
});

it('sets missing parameter to its default value if available', function () {
    // Arrange
    $values = ['property1' => 'value1'];

    // Act
    $instance = Subclass::createFromArray($values);

    // Assert
    expect($instance->property1)->toBe('value1');
    expect($instance->property2)->toBe(null);
    expect($instance->property3)->toBe('default');
});

// it('throws an exception when a non-nullable parameter has a null value', function () {
//     // Arrange
//     $values = ['property1' => null];

//     // Act and Assert
//     expect(function () use ($values) {
//         Subclass::createFromArray($values);
//     })->toThrow(Exception::class);
// });

// it('throws an exception if the class does not exist', function () {
//     // Arrange
//     $values = [];

//     // Assert
//     expect(function () use ($values) {
//         // Act
//         UnknownClass::createFromArray($values);
//     })->toThrow(ReflectionException::class);
// });

// it('throws an exception if input array contains invalid key', function () {
//     // Arrange
//     $values = ['invalidKey' => 'value'];

//     // Assert
//     expect(function () use ($values) {
//         // Act
//         Subclass::createFromArray($values);
//     })->toThrow(Exception::class, 'Failed to instantiate DataTransferObject');
// });
