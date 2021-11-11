# Slim Framework Request Validation

A **Request** validation library for the Slim 4 Framework. It uses [Respect/Validation][respect-validation] library.

## Table of contents

- [Install](#install)
- [Usage](#usage)

## Install

Via Composer

``` bash
$ composer require benycode/slim-request-validation
```

Requires Slim 4.

## Usage

Create a **Rule** class:

```php
use BenyCode\Slim\Validation\Rule\RequestValidationRuleInterface;
use Respect\Validation\Validator;

final class AnyRuleClass implements RequestValidationRuleInterface
{
    public function rules(): array
    {
        return [
          'name' => Validator::alpha(),
          'url' => Validator::filterVar(FILTER_VALIDATE_URL),
        ];
    }
    
    public function messages(): array
    {
        return [
            'filterVar' => '{{name}} must be valid modified message.',
        ];
    }
}
```

add a **Middlewares** to route:

```php
use BenyCode\Slim\Validation\Middleware\RequestValidationExceptionMiddleware;
use BenyCode\Slim\Validation\Transformer\RequestValidatorTransformFactory;
use BenyCode\Slim\Validation\Encoder\JsonEncoder;

$app = new \Slim\App();

$app->post('/api/any_end_point',function ($req, $res, $args) {
 
})
->add(new RequestValidationExceptionMiddleware(new RequestValidatorTransformFactory(), new JsonEncoder()))
->add(new RequestValidation([
	new AnyRuleClass(),
]))	
;

$app->run();
```

keep a clean code and split the rules:

```php
use BenyCode\Slim\Validation\Middleware\RequestValidationExceptionMiddleware;
use BenyCode\Slim\Validation\Transformer\RequestValidatorTransformFactory;
use BenyCode\Slim\Validation\Encoder\JsonEncoder;

$app = new \Slim\App();

$app->post('/api/any_end_point',function ($req, $res, $args) {
 
})
->add(new RequestValidationExceptionMiddleware(new RequestValidatorTransformFactory(), new JsonEncoder()))
->add(new RequestValidation([
	new AnyRuleClass(),
	new AppendedRuleClass(),
	new AppendedRule2Class(),
	new AppendedRule3Class(),
	....
]))	
;

$app->run();
```