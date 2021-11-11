<?php

namespace BenyCode\Slim\Validation\Tests\Rule;

use BenyCode\Slim\Validation\Rule\RequestValidationRuleInterface;
use Respect\Validation\Validator;

final class NameRequestValidationRule implements RequestValidationRuleInterface
{
    public function rules(): array
    {

        return [
          'name' => (new Validator())->alpha(),
        ];
    }
    
    public function messages(): array
    {
        
        return [
            
        ];
    }
}
