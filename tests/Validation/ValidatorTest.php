<?php

use Denosys\App\Database\Entities\User;
use Denosys\Core\Validation\ValidationException;
use Denosys\Core\Validation\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

beforeEach(function () {
    $this->validator = new Validator();
    $this->entityManager = $this->createMock(EntityManagerInterface::class);
    $this->repository = $this->createMock(EntityRepository::class);
    $this->validator->setValidationEntityManager($this->entityManager);
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
        'age' => 'twenty'
    ];
    $rules = [
        'name' => ['required'],
        'email' => ['required', 'email'],
        'age' => ['required', 'numeric']
    ];
    $this->validator->validate($data, $rules);
})->throws(ValidationException::class, 'Form validation error(s)');

test('apply unique rule successfully', function () {
    $data = ['email' => 'john@example.com'];
    $rules = ['email' => ['unique:users']];

    $this->repository->expects($this->once())
        ->method('count')
        ->with($data)
        ->willReturn(0);

    $this->entityManager->expects($this->once())
        ->method('getRepository')
        ->with(User::class)
        ->willReturn($this->repository);

    $result = $this->validator->validate($data, $rules);

    expect($result)->toBe($data);
});

test('validate unique constraint violation', function () {
    $data = ['email' => 'john@example.com'];
    $rules = ['email' => ['unique:users']];

    $this->repository->expects($this->once())
        ->method('count')
        ->with($data)
        ->willReturn(1);

    $this->entityManager->expects($this->once())
        ->method('getRepository')
        ->with($this->equalTo('Denosys\\App\\Database\\Entities\\User'))
        ->willReturn($this->repository);

    $this->validator->validate($data, $rules);
})->throws(ValidationException::class);

test('validate unique constraint with non existing field', function () {
    $data = ['non existing field' => 'value'];
    $rules = ['non existing field' => ['unique:users']];

    $this->validator->validate($data, $rules);
})->throws(ValidationException::class);

test('validate unique constraint with null value', function () {
    $data = ['email' => null];
    $rules = ['email' => ['unique:users']];

    $this->validator->validate($data, $rules);
})->throws(ValidationException::class);

test('validated returns validated data when no errors', function () {
    $data = ['name' => 'Jane Doe'];

    $this->validator->validate($data, ['name' => ['required']]);
    $validatedData = $this->validator->validated();

    expect($validatedData)->toBe($data);
});

test('validated throws exception when errors present', function () {
    $data = ['name' => ''];

    $this->validator->validate($data, ['name' => ['required']]);
    $this->validator->validated();
})->throws(ValidationException::class);

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
