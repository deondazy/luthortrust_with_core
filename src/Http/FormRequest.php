<?php

declare(strict_types=1);

namespace Denosys\Core\Http;

use Denosys\Core\Validation\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\ServerRequestInterface;
use Denosys\Core\Validation\ValidationException;

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
     * Form field errors
     * 
     * @var array
     */
    protected array $bodyErrors = [];

    /**
     * File errors
     * 
     * @var array
     */
    protected array $fileErrors = [];

    /**
     * Get the validation rules that apply to the request.
     * 
     * @return array
     */
    abstract public function rules(): array;

    /**
     * Get the uploaded files from the request.
     * 
     * @return array
     */
    public function files(): array
    {
        return $this->request->getUploadedFiles();
    }

    /**
     * Get specific uploaded file from the request.
     * 
     * @param string $key

     * @return UploadedFileInterface|null
     */
    public function file(string $key): ?UploadedFileInterface
    {
        $files = $this->files();
        return $files[$key] ?? null;
    }

    /**
     * Determine if request has a given file.
     * 
     * @param string $key
     *
     * @return bool
     */
    public function hasFile(string $key): bool
    {
        return $this->file($key) !== null;
    }

    public function validate(): array
    {
        // $rules = $this->rules();
        // $data = $this->request->getParsedBody();

        // $this->validator = new Validator();
        // $this->validator->setValidationEntityManager(
        //     container(EntityManagerInterface::class)
        // );

        // return $this->validator->validate($data, $rules);

        $this->validateBody();
        $this->validateFiles();

        $errors = array_merge($this->bodyErrors, $this->fileErrors);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return $this->validated();
    }

    public function validated(): array
    {
        return $this->validator->validated();
    }

    public function setServerRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    private function validateBody(): array
    {
        $rules = $this->rules();
        $data = $this->request->getParsedBody();

        $this->validator = new Validator();
        $this->validator->setValidationEntityManager(
            container(EntityManagerInterface::class)
        );

        try {
            return $this->validator->validate($data, $rules);
        } catch (ValidationException $e) {
            $this->bodyErrors = $e->getErrors();
            return $this->bodyErrors;
        }
    }

    private function validateFiles(): array
    {
        $errors = [];
        $files = $this->files();

        foreach ($this->rules() as $field => $rules) {
            // TODO: use Arr class to check if the field is an array.
            if (array_key_exists($field, $files)) {
                $file = $files[$field];
                // $errors[$field] = $this->validateSingleFile($file, $rules);

                // Check if a file was uploaded and is valid
                if ($file instanceof UploadedFileInterface) {
                    // if ($file->getError() === UPLOAD_ERR_NO_FILE) {
                    //     if (in_array('required', $rules)) {
                    //         $errors[$field] = 'The file is required.';
                    //     }
                    //     // No file was uploaded for this field, skip further validation
                    //     continue;
                    // } elseif ($file->getError() !== UPLOAD_ERR_OK) {
                    //     // Other errors occurred during file upload
                    //     $errors[$field] = 'An error occurred during file upload.';
                    //     continue;
                    // }

                    // // Proceed with further validation if the file is valid
                    $fileErrors = $this->validateSingleFile($file, $rules);
                    if (!empty($fileErrors)) {
                        $errors[$field] = $fileErrors;
                    }
                }
            }
        }

        $this->fileErrors = array_filter($errors);

        return $this->fileErrors;
    }

    private function validateSingleFile(UploadedFileInterface $file, array $rules): array
    {
        $errors = [];

        // TODO: Use the Validator to validate the file.
        if (in_array('required', $rules) && $file->getError() === UPLOAD_ERR_NO_FILE) {
            $errors[] = 'The file is required.';
        }

        return $errors;
    }
}
