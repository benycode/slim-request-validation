<?php
declare(strict_types=1);

namespace BenyCode\Slim\Validation\Transformer;

final class RequestValidatorTransformFactory implements ResultTransformerInterface
{
    public function transform(array $errors): array
    {
        return [
            'message' => 'Please check your input',
            'error' => $errors,
        ];
    }
    
    public function setLocale(string $locale): self;
}
