<?php

namespace BenyCode\Slim\Validation\Tests;

use BenyCode\Slim\Validation\RequestValidation;
use BenyCode\Slim\Validation\Tests\Rule\UrlRequestValidationRule;
use BenyCode\Slim\Validation\Tests\Rule\NameRequestValidationRule;
use BenyCode\Slim\Validation\Tests\Rule\UrlRequestValidationRuleWithTranslate;
use BenyCode\Slim\Validation\Tests\Rule\MoreThenOneRequestValidationRule;
use BenyCode\Slim\Validation\Middleware\RequestValidationExceptionMiddleware;
use BenyCode\Slim\Validation\Transformer\RequestValidatorTransformFactory;
use BenyCode\Slim\Validation\Encoder\JsonEncoder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ValidationTest extends TestCase
{
    public function testValidationEmptyPostBody()
    {
        $app = $this->getAppInstance();

        $app->post('/foo', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
            return $response;
        })
        ->add(new RequestValidationExceptionMiddleware(new RequestValidatorTransformFactory(), new JsonEncoder()))
        ->add(new RequestValidation([new UrlRequestValidationRule()]))
        ;

        $request = $this->createJsonRequest('POST', '/foo');

        $response = $app->handle($request);

        $result = (string) $response->getBody();

        $this->assertEquals(422, $response->getStatusCode());
        
        $response = '{"message":"Please check your input","error":{"url":{"filterVar":"\"\" must be valid"}}}';
        
        $this->assertEquals($response, $result);
    }
    
    public function testValidationEmptyPostBodyMoreThenOneParameter()
    {
        $app = $this->getAppInstance();

        $app->post('/foo', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
            return $response;
        })
        ->add(new RequestValidationExceptionMiddleware(new RequestValidatorTransformFactory(), new JsonEncoder()))
        ->add(new RequestValidation([new MoreThenOneRequestValidationRule()]))
        ;

        $request = $this->createJsonRequest('POST', '/foo');

        $response = $app->handle($request);

        $result = (string) $response->getBody();

        $this->assertEquals(422, $response->getStatusCode());
        
        $response = [
            'message' => 'Please check your input',
            'error' => [
                'name' => [
                    'alpha' => '"" must contain only letters (a-z)',
                ],
                'url' => [
                    'filterVar' => '"" must be valid',
                ],
            ],
        ];
        
        $this->assertEquals(\json_encode($response), $result);
    }
    
    public function testValidationEmptyPostBodyWithTranslate()
    {
        $app = $this->getAppInstance();

        $app->post('/foo', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
            return $response;
        })
        ->add(new RequestValidationExceptionMiddleware(new RequestValidatorTransformFactory(), new JsonEncoder()))
        ->add(new RequestValidation([new UrlRequestValidationRuleWithTranslate()]))
        ;

        $request = $this->createJsonRequest('POST', '/foo');

        $response = $app->handle($request);

        $result = (string) $response->getBody();

        $this->assertEquals(422, $response->getStatusCode());

        $response = [
            'message' => 'Please check your input',
            'error' => [
                'url' => [
                    'filterVar' => '"" must be valid new message',
                ],
            ],
        ];
        
        $this->assertEquals(\json_encode($response), $result);
    }
    
    public function testValidationIncorrectPostBody()
    {
        $app = $this->getAppInstance();

        $app->post('/foo', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
            return $response;
        })
        ->add(new RequestValidationExceptionMiddleware(new RequestValidatorTransformFactory(), new JsonEncoder()))
        ->add(new RequestValidation([new UrlRequestValidationRule()]))
        ;

        $data = [
            'url' => 'test',
        ];

        $request = $this->createJsonRequest('POST', '/foo', $data);

        $response = $app->handle($request);

        $result = (string) $response->getBody();

        $this->assertEquals(422, $response->getStatusCode());

        $response = [
            'message' => 'Please check your input',
            'error' => [
                'url' => [
                    'filterVar' => '"test" must be valid',
                ],
            ],
        ];
        
        $this->assertEquals(\json_encode($response), $result);
    }
    
    public function testValidationCorrectPostBody()
    {
        $app = $this->getAppInstance();

        $app->post('/foo', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
            return $response;
        })
        ->add(new RequestValidationExceptionMiddleware(new RequestValidatorTransformFactory(), new JsonEncoder()))
        ->add(new RequestValidation([new UrlRequestValidationRule()]))
        ;

        $data = [
            'url' => 'https://test.com',
        ];

        $request = $this->createJsonRequest('POST', '/foo', $data);

        $response = $app->handle($request);

        $result = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
    }
    
    public function testValidationEmptyPostBodyWithTwoValidations()
    {
        $app = $this->getAppInstance();

        $app->post('/foo', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
            return $response;
        })
        ->add(new RequestValidationExceptionMiddleware(new RequestValidatorTransformFactory(), new JsonEncoder()))
        ->add(new RequestValidation([new UrlRequestValidationRule(), new NameRequestValidationRule()]))
        ;

        $request = $this->createJsonRequest('POST', '/foo');

        $response = $app->handle($request);

        $result = (string) $response->getBody();

        $this->assertEquals(422, $response->getStatusCode());

        $response = [
            'message' => 'Please check your input',
            'error' => [
                'url' => [
                    'filterVar' => '"" must be valid',
                ],
                'name' => [
                    'alpha' => '"" must contain only letters (a-z)',
                ],
            ],
        ];
        
        $this->assertEquals(\json_encode($response), $result);
    }
    
    public function testValidationEmptyGet()
    {
        $app = $this->getAppInstance();

        $app->get('/foo', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
            return $response;
        })
        ->add(new RequestValidationExceptionMiddleware(new RequestValidatorTransformFactory(), new JsonEncoder()))
        ->add(new RequestValidation([new UrlRequestValidationRule()]))
        ;
        
        $params = [];

        $url = \sprintf('/foo?%s', \http_build_query($params));
        
        $request = $this->createRequest('GET', $url);

        $response = $app->handle($request);

        $result = (string) $response->getBody();

        $this->assertEquals(422, $response->getStatusCode());
        
        $response = [
            'message' => 'Please check your input',
            'error' => [
                'url' => [
                    'filterVar' => '"" must be valid',
                ],
            ],
        ];
        
        $this->assertEquals(\json_encode($response), $result);
    }
    
    public function testValidationCorrectGet()
    {
        $app = $this->getAppInstance();

        $app->get('/foo', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
            return $response;
        })
        ->add(new RequestValidationExceptionMiddleware(new RequestValidatorTransformFactory(), new JsonEncoder()))
        ->add(new RequestValidation([new UrlRequestValidationRule()]))
        ;
        
        $params = [
            'url' => 'https://example.com'
        ];

        $url = \sprintf('/foo?%s', \http_build_query($params));
        
        $request = $this->createRequest('GET', $url);

        $response = $app->handle($request);

        $result = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
    }
    
    public function testValidationEmptyPostBodyWithTwoValidationsAndOneCorrect()
    {
        $app = $this->getAppInstance();

        $app->post('/foo', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
            return $response;
        })
        ->add(new RequestValidationExceptionMiddleware(new RequestValidatorTransformFactory(), new JsonEncoder()))
        ->add(new RequestValidation([new UrlRequestValidationRule(), new NameRequestValidationRule()]))
        ;
        
        $data = [
            'url' => 'https://test.com',
        ];

        $request = $this->createJsonRequest('POST', '/foo', $data);

        $response = $app->handle($request);

        $result = (string) $response->getBody();

        $this->assertEquals(422, $response->getStatusCode());

        $response = [
            'message' => 'Please check your input',
            'error' => [
                'name' => [
                    'alpha' => '"" must contain only letters (a-z)',
                ],
            ],
        ];
        
        $this->assertEquals(\json_encode($response), $result);
    }
}
