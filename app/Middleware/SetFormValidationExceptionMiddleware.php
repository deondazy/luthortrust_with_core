<?php

declare(strict_types=1);

namespace Denosys\App\Middleware;

use Denosys\Core\FormValidation\FormValidationException;
use Denosys\Core\Http\RedirectResponse;
use Denosys\Core\Session\SessionInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class SetFormValidationExceptionMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly SessionInterface $session)
    {
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        try {
            return $handler->handle($request);
        } catch (FormValidationException $exception) {
            $oldFormData = $request->getParsedBody();

            // TODO: This is a temporary fix. We need to find a better way to handle this.
            $sensitiveFormFields = ['password', 'confirmPassword'];

            $this->session->getFlash()->set(
                'oldFormData',
                array_diff_key($oldFormData, array_flip($sensitiveFormFields))
            );
            $this->session->getFlash()->set('errors', $exception->getErrors());

            return new RedirectResponse($request->getServerParams()['HTTP_REFERER']);
        }
    }
}
