<?php

use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityRepository;
use Denosys\App\Database\Entities\User;
use Doctrine\ORM\EntityManagerInterface;
use Denosys\Core\Form\Validation\Validator;
use Denosys\Core\Form\Validation\ValidationException;

class ValidatorTest extends TestCase
{
    /**
     * @covers Validator::validate
     */
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

        $result = (new Validator)->validate($data, $rules);

        $this->assertTrue($result);
    }

    /**
     * @covers Validator::validate
     */
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

        (new Validator)->validate($data, $rules);
    }

    /**
     * @covers Validator::validate
     */
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

        (new Validator)->validate($data, $rules);
    }

    /**
     * @covers Validator::validate
     */
    public function test_apply_unique_rule_successfully(): void
    {
        // Arrange
        $data = ['email' => 'john@example.com'];
        $rules = ['email' => ['unique:users']];
        $validator = new Validator();

        // Mock the EntityManagerInterface
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->once())
            ->method('count')
            ->with(['email' => 'john@example.com'])
            ->willReturn(0);

        // Use a stub class name instead of the actual User entity
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($repository);
        $validator->setValidationEntityManager($entityManager);

        $result = $validator->validate($data, $rules);

        $this->assertTrue($result);
    }
}