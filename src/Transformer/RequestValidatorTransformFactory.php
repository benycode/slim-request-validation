<?php
declare(strict_types=1);

namespace BenyCode\Slim\Validation\Transformer;

final class RequestValidatorTransformFactory implements ResultTransformerInterface
{
    public function __construct(protected ?string $message = null) {
    }
    
    public function transform(array $errors): array
    {
        return [
            'message' => is_null(this->message) ? 'Please check your input' : this->message,
            'error' => $errors,
        ];
    }
}
