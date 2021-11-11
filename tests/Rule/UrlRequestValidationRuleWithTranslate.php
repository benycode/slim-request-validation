<?php

namespace BenyCode\Slim\Validation\Tests\Rule;

use BenyCode\Slim\Validation\Rule\RequestValidationRuleInterface;
use Respect\Validation\Validator;

final class UrlRequestValidationRuleWithTranslate implements RequestValidationRuleInterface
{
    public function rules(): array
    {

        return [
          'url' => (new Validator())->filterVar(FILTER_VALIDATE_URL),
        ];
    }
    
    public function messages(): array
    {
        
        return [
            'filterVar' => '{{name}} must be valid new message',
        ];
    }
}
