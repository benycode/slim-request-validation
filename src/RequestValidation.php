<?php
declare(strict_types=1);

namespace BenyCode\Slim\Validation;

use BenyCode\Slim\Validation\Rule\RequestValidationRuleInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator;
use Slim\Routing\RouteContext;

class RequestValidation
{
    protected array $rules = [];
    
    protected array $validators = [];
    
    protected array $messages = [];

    protected array $options = [];

    protected ?array $translator = null;

    protected array $errors = [];

    protected string $errors_name = 'errors';

    protected string $has_errors_name = 'has_errors';

    protected string $validators_name = 'validators';
    
    protected string $messages_name = 'messages';

    protected string $translator_name = 'translator';

    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {        
        if ((bool) $this->rules) {
            /** @var RequestValidationRuleInterface $validator */
            foreach ($this->rules as $rule) {
                $this->validators = \array_merge($this->validators, $rule->rules());
                if ((bool)$messages = $rule->messages()) {
                    $this->messages = \array_merge($this->messages, $messages);
                }
            }
        } else {
            $this->validators = [];
            $this->messages = [];
        }
        
        $this->errors = [];
        $params = $request->getParsedBody();

        $routeContext = RouteContext::fromRequest($request);
        
        $route = $routeContext->getRoute();
        
        $arguments = [];
        $queryParams = [];
        
        if ((bool) $route) {
            $arguments = $route->getArguments();
            $queryParams = $request->getQueryParams();
        }

        $params = \array_merge($arguments, (array) $params, $queryParams);
        
        $this->validate($params, $this->validators);

        $request = $request->withAttribute($this->errors_name, $this->getErrors());
        $request = $request->withAttribute($this->has_errors_name, $this->hasErrors());
        $request = $request->withAttribute($this->validators_name, $this->getValidators());
        $request = $request->withAttribute($this->messages_name, $this->getMessages());
        $request = $request->withAttribute($this->translator_name, $this->getTranslator());

        return $handler->handle($request);
    }

    private function validate(array $params = [], array $validators = [], array $actualKeys = []): void
    {
        /** @var mixed $validator */
        foreach ($validators as $key => $validator) {
            $actualKeys[] = $key;
            $param = (string) $this->getNestedParam($params, $actualKeys);
            if (\is_array($validator)) {
                $this->validate($params, $validator, $actualKeys);
            } else {
                try {
                    /** @var Validator $validator */
                    $validator->assert($param);
                } catch (NestedValidationException $exception) {
                    if ((bool) $this->getMessages()) {
                        $this->translator = $exception->getMessages($this->getMessages());
                    }
                    $this->errors[\implode('.', $actualKeys)] = $exception->getMessages();
                }
            }
            
            \array_pop($actualKeys);
        }
    }
    
    /** @param mixed $params */
    private function getNestedParam($params = [], array $keys = []): ?string
    {
        if (\count($keys) === 0) {
            /** @var string */
            return $params;
        }
        $firstKey = (string) \array_shift($keys);
        if ($this->isArrayLike($params) && \array_key_exists($firstKey, (array) $params)) {
            $params = (array) $params;
            $paramValue = (string) $params[$firstKey];
            return $this->getNestedParam($paramValue, $keys);
        }
        return null;
    }

    /** @param mixed $params */
    private function isArrayLike($params): bool
    {
        return \is_array($params) || $params instanceof \SimpleXMLElement;
    }

    public function hasErrors(): bool
    {
        return (bool) $this->errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getValidators(): array
    {
        return $this->validators;
    }
    
    public function getMessages(): array
    {
        return $this->messages;
    }

    public function setValidators(array $validators): void
    {
        $this->validators = $validators;
    }

    public function getTranslator(): ?array
    {
        return $this->translator;
    }

    public function setTranslator(?array $translator): void
    {
        $this->translator = $translator;
    }
}
