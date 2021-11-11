<?php

namespace BenyCode\Slim\Validation\Tests\Rule;

use BenyCode\Slim\Validation\Rule\RequestValidationRuleInterface;
use Respect\Validation\Validator;

final class MoreThenOneRequestValidationRule implements RequestValidationRuleInterface
{
    public function rules(): array
    {

        return [
          'name' => (new Validator())->alpha(),
          'url' => (new Validator())->filterVar(FILTER_VALIDATE_URL),
        ];
    }
    
    public function messages(): array
    {
        
        return [
            
        ];
    }
}
