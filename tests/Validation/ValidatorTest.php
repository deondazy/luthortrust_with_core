<?php

use Denosys\App\Database\Entities\User;
use Denosys\Core\Validation\ValidationException;
use Denosys\Core\Validation\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

beforeEach(function () {
    $this->validator = new Validator();
    $this->entityManager = $this->createMock(EntityManagerInterface::class);
    $this->repository = $this->createMock(EntityRepository::class);
    // $this->validator->setValidationEntityManager($this->entityManager);
});

test('validation passes', function () {
    $data = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'age' => 25
    ];
    $rules = [
        'name' => ['required'],
        'email' => ['required', 'email'],
        'age' => ['required', 'numeric']
    ];

    $result = $this->validator->validate($data, $rules);

    expect($data)->toBe($result);
});

test('validation fails', function () {
    $data = [
        'name' => null,
        'email' => 'john@example',
        'age' => 'twenty'
    ];
    $rules = [
        'name' => ['required'],
        'email' => ['required', 'email'],
        'age' => ['required', 'numeric']
    ];

    $this->validator->validate($data, $rules);
})->throws(ValidationException::class, 'Form validation error(s)');

test('standard validation rules', function () {
    $data = [
        'name' => '',
        'email' => 'john@example',
        'age' => 'twenty',
        'too_short' => 'abc',
        'too_long' => 'abcdefghijk',
    ];
    $rules = [
        'name' => ['required'],
        'email' => ['required', 'email'],
        'age' => ['required', 'numeric'],
        'too_short' => ['min:5'],
        'too_long' => ['max:5']
    ];
    $this->validator->validate($data, $rules);
})->throws(ValidationException::class, 'Form validation error(s)');

test('validated returns validated data when no errors', function () {
    $data = ['name' => 'Jane Doe'];

    $this->validator->validate($data, ['name' => ['required']]);
    $validatedData = $this->validator->validated();

    expect($validatedData)->toBe($data);
});

test('validated throws exception when errors present', function () {
    $data = ['name' => ''];


    expect(function () use ($data) {
        $this->validator->validate($data, ['name' => ['required']]);
        $this->validator->validated();
    })->toThrow(ValidationException::class);
});

test('fails returns true when validation fails', function () {
    $data = ['name' => ''];

    $this->validator->validate($data, ['name' => ['required']]);
    $this->assertTrue($this->validator->fails());
})->throws(ValidationException::class);

test('fails returns false when validation passes', function () {
    $data = ['name' => 'Jane Doe'];

    $this->validator->validate($data, ['name' => ['required']]);

    expect($this->validator->fails())->toBeFalse();
});

test('errors returns errors when validation fails', function () {
    $data = ['name' => ''];

    $this->validator->validate($data, ['name' => ['required']]);
    $errors = $this->validator->errors();

    expect($errors)->toHaveKey('name')
        ->and($errors['name'])->not->toBeEmpty()
        ->and($errors['name'])->toBe('Name is required.');
})->throws(ValidationException::class);

test('errors returns empty array when validation passes', function () {
    $data = ['name' => 'Jane Doe'];

    $this->validator->validate($data, ['name' => ['required']]);

    expect($this->validator->errors())->toBeEmpty();
});

test('validation fails and throws exception with get errors containg all errors', function () {
    $data = [
        'name' => '',
        'email' => 'john@example',
        'age' => 'twenty',
        'too_short' => 'abc',
        'too_long' => 'abcdefghijk',
    ];
    $rules = [
        'name' => ['required'],
        'email' => ['required', 'email'],
        'age' => ['required', 'numeric'],
        'too_short' => ['min:5'],
        'too_long' => ['max:5']
    ];

    try {
        $this->validator->validate($data, $rules);
    } catch (ValidationException $e) {
        $errors = $e->getErrors();
    }

    expect($errors)->toHaveCount(5)
        ->and($errors)->toHaveKey('name')
        ->and($errors)->toHaveKey('email')
        ->and($errors)->toHaveKey('age')
        ->and($errors)->toHaveKey('too_short')
        ->and($errors)->toHaveKey('too_long')
        ->and($errors['name'])->not->toBeEmpty()
        ->and($errors['name'][0])->toBe('Name is required')
        ->and($errors['email'])->not->toBeEmpty()
        ->and($errors['email'][0])->toBe('Email is not a valid email address')
        ->and($errors['age'])->not->toBeEmpty()
        ->and($errors['age'][0])->toBe('Age must be numeric')
        ->and($errors['too_short'])->not->toBeEmpty()
        ->and($errors['too_short'][0])->toBe('Too Short must be at least 5 characters long')
        ->and($errors['too_long'])->not->toBeEmpty()
        ->and($errors['too_long'][0])->toBe('Too Long must not exceed 5 characters');
});
