<?php
declare(strict_types=1);

namespace BenyCode\Slim\Validation\Middleware;

use Slim\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use BenyCode\Slim\Validation\Encoder\EncoderInterface;
use BenyCode\Slim\Validation\Transformer\ResultTransformerInterface;
use Fig\Http\Message\StatusCodeInterface;

final class RequestValidationExceptionMiddleware implements MiddlewareInterface
{
    private ResultTransformerInterface $transformer;

    private EncoderInterface $encoder;

    public function __construct(
        ResultTransformerInterface $transformer,
        EncoderInterface $encoder
    ) {
        $this->transformer = $transformer;
        $this->encoder = $encoder;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ((bool)$request->getAttribute('has_errors')) {
            $response = (new Response())
                ->withStatus(StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY)
                ->withHeader('Content-Type', $this->encoder->getContentType());

            $errors = (array) $request
                ->getAttribute('errors')
            ;
            
            $locale = $request
                ->getAttribute('accept-language')
            ;
            
            $data = $this
                ->transformer
                ->setLocale($locale)
                ->transform($errors)
            ;
            
            $content = $this
                ->encoder
                ->encode($data)
            ;
            
            $response
                ->getBody()
                ->write($content)
            ;

            return $response;
        }
        
        return $handler->handle($request);
    }
}
