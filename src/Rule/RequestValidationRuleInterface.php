<?php
declare(strict_types=1);

namespace BenyCode\Slim\Validation\Rule;

interface RequestValidationRuleInterface
{
    public function rules(): array;
   
    public function messages(string $language = null): array;
}
