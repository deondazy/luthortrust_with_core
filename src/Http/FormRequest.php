<?php

declare(strict_types=1);

namespace Denosys\Core\Http;

use Denosys\Core\Validation\Validator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class FormRequest
{
    /**
     * Validator instance
     * 
     * @var Validator
     */
    protected Validator $validator;

    /**
     * ServerRequestInterface instance
     * 
     * @var ServerRequestInterface
     */
    protected ServerRequestInterface $request;

    /**
     * Get the validation rules that apply to the request.
     * 
     * @return array
     */
    abstract public function rules(): array;

    public function validate(): array
    {
        $rules = $this->rules();
        $data = $this->request->getParsedBody();

        $this->validator = new Validator();
        $this->validator->setValidationEntityManager(
            app()->getContainer()->get(EntityManagerInterface::class)
        );

        return $this->validator->validate($data, $rules);
    }

    public function validated(): array
    {
        return $this->validator->validated();
    }

    public function setServerRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }
}
