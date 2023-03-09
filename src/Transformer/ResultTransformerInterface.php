<?php
declare(strict_types=1);

namespace BenyCode\Slim\Validation\Transformer;

interface ResultTransformerInterface
{
    public function transform(array $errors): array;
    
    public function setLocale(?string $locale = null): self;
}
