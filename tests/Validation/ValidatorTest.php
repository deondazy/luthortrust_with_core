<?php

use Denosys\App\Database\Entities\User;
use Denosys\Core\Validation\ValidationException;
use Denosys\Core\Validation\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    private Validator $validator;
    private EntityManagerInterface $entityManager;
    private EntityRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new Validator();
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->repository = $this->createMock(EntityRepository::class);
        $this->validator->setValidationEntityManager($this->entityManager);
    }

    public function test_validation_passes(): void
    {
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

        $this->assertSame($data, $result);
    }

    public function test_validation_fails(): void
    {
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

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Form validation error(s)');
        $this->expectExceptionCode(422);

        $this->validator->validate($data, $rules);
    }

    public function test_standard_validation_rules(): void
    {
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

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Form validation error(s)');
        $this->expectExceptionCode(422);

        $this->validator->validate($data, $rules);
    }

    public function test_apply_unique_rule_successfully(): void
    {
        $data = ['email' => 'john@example.com'];
        $rules = ['email' => ['unique:users']];

        $this->repository->expects($this->once())
            ->method('count')
            ->with(['email' => 'john@example.com'])
            ->willReturn(0);

        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($this->repository);

        $result = $this->validator->validate($data, $rules);

        $this->assertSame($data, $result);
    }

    public function test_validate_unique_constraint_violation(): void
    {
        $data = ['email' => 'john@example.com'];
        $rules = ['email' => ['unique:users']];

        $this->repository->expects($this->once())
            ->method('count')
            ->with(['email' => 'john@example.com'])
            ->willReturn(1);

        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('Denosys\\App\\Database\\Entities\\User'))
            ->willReturn($this->repository);

        $this->expectException(ValidationException::class);

        $this->validator->validate($data, $rules);
    }

    public function test_validate_unique_constraint_with_non_existing_field(): void
    {
        $data = ['non_existing_field' => 'value'];
        $rules = ['non_existing_field' => ['unique:users']];

        $this->expectException(ValidationException::class);

        $this->validator->validate($data, $rules);
    }

    public function test_validate_unique_constraint_with_null_value(): void
    {
        $data = ['email' => null];
        $rules = ['email' => ['unique:users']];

        $this->expectException(ValidationException::class);

        $this->validator->validate($data, $rules);
    }

    public function test_validated_returns_validated_data_when_no_errors(): void
    {
        $data = ['name' => 'Jane Doe'];

        $this->validator->validate($data, ['name' => ['required']]);

        $validatedData = $this->validator->validated();

        $this->assertSame($data, $validatedData);
    }

    public function test_validated_throws_exception_when_errors_present(): void
    {
        $data = ['name' => ''];

        $this->expectException(ValidationException::class);

        $this->validator->validate($data, ['name' => ['required']]);
        $this->validator->validated();
    }

    public function test_fails_returns_true_when_validation_fails(): void
    {
        $data = ['name' => ''];

        $this->expectException(ValidationException::class);

        $this->validator->validate($data, ['name' => ['required']]);
        $this->assertTrue($this->validator->fails());
    }
    public function test_fails_returns_false_when_validation_passes(): void
    {
        $data = ['name' => 'Jane Doe'];

        $this->validator->validate($data, ['name' => ['required']]);

        $this->assertFalse($this->validator->fails());
    }

    public function test_errors_returns_errors_when_validation_fails(): void
    {
        $data = ['name' => ''];

        $this->expectException(ValidationException::class);

        $this->validator->validate($data, ['name' => ['required']]);
        $errors = $this->validator->errors();

        $this->assertArrayHasKey('name', $errors);
        $this->assertNotEmpty($errors['name']);
    }

    public function test_errors_returns_empty_array_when_validation_passes(): void
    {
        $data = ['name' => 'Jane Doe'];

        $this->validator->validate($data, ['name' => ['required']]);

        $errors = $this->validator->errors();

        $this->assertEmpty($errors);
    }
}
