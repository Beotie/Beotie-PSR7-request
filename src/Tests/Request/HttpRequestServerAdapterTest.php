<?php
declare(strict_types=1);
/**
 * This file is part of beotie/core_bundle
 *
 * As each files provides by the CSCFA, this file is licensed
 * under the MIT license.
 *
 * PHP version 7.1
 *
 * @category Test
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
namespace Beotie\PSR7\Tests\Request;

use Beotie\PSR7\Request\HttpRequestServerAdapter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Beotie\PSR7\Request\File\Factory\EmbeddedFileFactoryInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\ServerBag;
use Symfony\Component\OptionsResolver\OptionsResolver;
use PHPUnit\Framework\MockObject\MockObject;
use Beotie\PSR7\Tests\Request\HttpRequestServerAdapter as TestTrait;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use Symfony\Component\HttpFoundation\HeaderBag;

/**
 * HttpRequestServerAdapter test
 *
 * This class is used to validate the HttpRequestServerAdapter instance
 *
 * @category Test
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
class HttpRequestServerAdapterTest extends TestCase
{
    use TestTrait\UriTestTrait,
        TestTrait\MethodTestTrait,
        TestTrait\RequestTargetTestTrait,
        TestTrait\QueryTestTrait,
        TestTrait\ProtocolTestTrait,
        TestTrait\HeaderTestTrait,
        TestTrait\FileTestTrait,
        TestTrait\DuplicationTestTrait,
        TestTrait\CookieTestTrait,
        TestTrait\BodyTestTrait;

    /**
     * Test construct
     *
     * This method validate the HttpRequestServerAdapter::__construct method
     *
     * @return void
     * @covers Beotie\PSR7\Request\HttpRequestServerAdapter::__construct
     */
    public function testConstruct() : void
    {
        $httpRequest = $this->getRequest();
        $fileFactory = $this->createMock(EmbeddedFileFactoryInterface::class);

        $instance = new HttpRequestServerAdapter($httpRequest, $fileFactory);
        $reflex = new \ReflectionClass(HttpRequestServerAdapter::class);

        $requestProperty = $reflex->getProperty('httpRequest');
        $requestProperty->setAccessible(true);

        $this->assertSame($httpRequest, $requestProperty->getValue($instance));

        $factoryProperty = $reflex->getProperty('fileFactory');
        $factoryProperty->setAccessible(true);

        $this->assertSame($fileFactory, $factoryProperty->getValue($instance));

        return;
    }

    /**
     * Test getServerParams
     *
     * Validate the HttpRequestServerAdapter::getServerParams by injecting mocked Request object. The injected request
     * is expected to return an array of server parameters and this method validate the return value.
     *
     * @return void
     */
    public function testGetServerParams() : void
    {
        $serverParam = ['server' => 'param'];

        $request = $this->getRequest([], ['server' => ['all' => $serverParam]]);

        $reflex = new \ReflectionClass(HttpRequestServerAdapter::class);
        $instance = $reflex->newInstanceWithoutConstructor();

        $requestProperty = $reflex->getProperty('httpRequest');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue($instance, $request);

        $this->assertEquals($serverParam, $instance->getServerParams());

        return;
    }

    /**
     * Get request
     *
     * Return an instance of Request mock that contain a ParameterBag mock in cookies, query and request properties
     * and contain a FileBag mock in file property and contain a ServerBag mock in server property.
     * For each properties, the instance will be configured with the according key given bag options. If the
     * configuration is not specified, the instance will return an empty array for each call of all() method.
     *
     * @param array $parameters An array of parameters to create invokation mocker into the returned instance
     * @param array $bagOptions An array containing the bag invocation configuration
     *
     * @return  MockObject
     * @example <pre>
     *  // Simple use
     *  $this->getRequest();
     *
     *  // Usage with invokation mocker
     *  $this->getRequest(
     *      [
     *          [
     *              'expects'    => $this->any(),
     *              'method'     => 'methodName',
     *              'willReturn' => null
     *          ]
     *      ]
     *  );
     *
     *  // Usage with bag invokation configuration
     *  $this->getRequest(
     *      [],
     *      [
     *          'server' => ['all' => []],
     *          'query' => ['any all' => []],
     *          'request' => [
     *              [
     *                  'expects'    => $this->any(),
     *                  'method'     => 'all',
     *                  'willReturn' => []
     *              ]
     *          ]
     *      ]
     *  );
     * </pre>
     */
    protected function getRequest(array $parameters = [], array $bagOptions = []) : MockObject
    {
        $httpRequest = $this->createMock(Request::class);
        $this->createInvocation($httpRequest, $parameters);

        $fallbackBagOptions = ['all' => []];
        $attributes = $this->createMock(ParameterBag::class);
        $this->createInvocation($attributes, ($bagOptions['attributes'] ?? $fallbackBagOptions));
        $httpRequest->attributes = $attributes;

        $cookies = $this->createMock(ParameterBag::class);
        $this->createInvocation($cookies, ($bagOptions['cookies'] ?? $fallbackBagOptions));
        $httpRequest->cookies = $cookies;

        $query = $this->createMock(ParameterBag::class);
        $this->createInvocation($query, ($bagOptions['query'] ?? $fallbackBagOptions));
        $httpRequest->query = $query;

        $request = $this->createMock(ParameterBag::class);
        $this->createInvocation($request, ($bagOptions['request'] ?? $fallbackBagOptions));
        $httpRequest->request = $request;

        $file = $this->createMock(FileBag::class);
        $this->createInvocation($file, ($bagOptions['files'] ?? $fallbackBagOptions));
        $httpRequest->files = $file;

        $server = $this->createMock(ServerBag::class);
        $this->createInvocation($server, ($bagOptions['server'] ?? $fallbackBagOptions));
        $this->createInvocation($server, [['expects'=>$this->any(), 'method'=>'get']]);
        $httpRequest->server = $server;

        $headers = $this->createMock(HeaderBag::class);
        $this->createInvocation($headers, ($bagOptions['headers'] ?? $fallbackBagOptions));
        $httpRequest->headers = $headers;

        return $httpRequest;
    }

    /**
     * Create invokation
     *
     * Add a new Matcher instance to the given mock instance by inserting it into the inner InvocationMocker
     * instance. The inserted Matcher is created accordingly with the given parameters options.
     * The applyable options are defined by a OptionsResolver instance given by getParamResolver.
     * A short notation is abble to be used to avoid using full explained option array by using associative
     * array. In this case, the given key define the method name to be called and the value become the return
     * value of the function. By using the gessInvocationTarget, it is possible to use space separated invocation
     * time and method name as key.
     *
     * @param MockObject $mock       The mock object to hydrate with a new Matcher instance
     * @param array      $parameters An array that configure the given MockObject's Matcher
     * @param bool       $firstLevel Internal use only
     *
     * @see     HttpRequestServerAdapterTest::getParamResolver
     * @return  void
     * @example <pre>
     *  // Full defined option
     *  $this->createInvocation(
     *      $mockObject,
     *      [
     *          [
     *              'expects'    => $this->any(),
     *              'method'     => 'methodName',
     *              'willReturn' => null
     *          ]
     *      ]
     *  );
     *
     *  // Same thing with shortcut notation
     *  $this->createInvocation(
     *      $mockObject,
     *      [
     *              'methodName' => null
     *      ]
     *  );
     *
     *  // Or with once invocation time
     *  $this->createInvocation(
     *      $mockObject,
     *      [
     *              'once methodName' => null
     *      ]
     *  );
     * </pre>
     */
    private function createInvocation(MockObject $mock, array $parameters, $firstLevel = true) : void
    {
        $resolver = $this->getParamResolver();

        foreach ($parameters as $parameterName => $parameter) {
            try {
                if (!is_array($parameter)) {
                    throw new InvalidArgumentException('Time to call callback');
                }

                $params = $resolver->resolve($parameter);
            } catch (InvalidArgumentException $exception) {
                unset($exception);
                list($invocationCount, $methodName) = $this->guessInvocationTarget($parameterName);

                $fallbackParameters = [
                    [
                        'expects' => $invocationCount,
                        'method' => $methodName,
                        'willReturn' => $parameter
                    ]
                ];

                $this->createInvocation($mock, $fallbackParameters, false);

                if (!$firstLevel) {
                    return;
                }
                continue;
            }

            $invoker = $mock->expects($params['expects']);

            foreach ($params as $key => $value) {
                if ($key == 'expects') {
                    continue;
                }

                $this->attachInvocationArgument($invoker, $key, $value);
            }
        }

        return;
    }

    /**
     * Attach invocation argument
     *
     * Call the argumentName method of the given invoker with the given argumentValue. If the argumentName is prefixed
     * by 'with' and the value is an array, the argumentValue will be evaluated as an array of function arguments.
     * Special invokation is reached on 'willReturnSelf' or 'withAnyParameters' argumentName; In this case, the method
     * is invoked without any paramters if argumentValue is true.
     *
     * @param InvocationMocker $invoker       The invokation mocker that will be configured
     * @param string           $argumentName  The method name to be called
     * @param mixed            $argumentValue The method argument
     *
     * @return void
     */
    private function attachInvocationArgument(InvocationMocker $invoker, string $argumentName, $argumentValue) : void
    {
        if (in_array($argumentName, ['willReturnSelf', 'withAnyParameters']) && (bool)$argumentValue) {
            $invoker->{$argumentName}();
            return;
        }

        if (preg_match('/(^with)|(Consecutive)/', $argumentName) && is_array($argumentValue)) {
            call_user_func_array([$invoker, $argumentName], $argumentValue);
            return;
        }

        $invoker->{$argumentName}($argumentValue);
        return;
    }

    /**
     * Guess invocation target
     *
     * Return an array corresponding to the parsing of the given fullTarget, representing the space separated expected
     * method invocation time and method invocation name.
     *
     * @param string $fullTarget The space separated representation of the method invocation time and method
     *                           invocation name
     *
     * @return  array
     * @example <pre>
     *  $this->gessInvocationTarget('any methodName'); // Will return [$this->any(), 'methodName']
     * </pre>
     */
    private function guessInvocationTarget(string $fullTarget) : array
    {
        $tokens = explode(' ', $fullTarget);

        if (count($tokens) === 1) {
            return [
                $this->any(),
                $tokens[0]
            ];
        }

        return [
            $this->{$tokens[0]}(),
            $tokens[1]
        ];
    }

    /**
     * Get param resolver
     *
     * Return an OptionsResolver instance that contain InvocationMocker methods as defined, and contain 'expects' as
     * required.
     *
     * @return OptionsResolver
     */
    private function getParamResolver() : OptionsResolver
    {
        $resolver = new OptionsResolver();

        $resolver->setRequired('expects');
        $resolver->setDefined(
            [
                'method',
                'will',
                'willReturn',
                'willReturnReference',
                'willReturnMap',
                'willReturnArgument',
                'willReturnCallback',
                'willReturnSelf',
                'willReturnOnConsecutiveCalls',
                'willThrowException',
                'with',
                'withConsecutive',
                'withAnyParameters'
            ]
        );

        $resolver->setAllowedTypes('willReturnMap', 'array');
        $resolver->setAllowedValues('willReturnSelf', [true]);
        $resolver->setAllowedValues('withAnyParameters', [true]);

        return $resolver;
    }

    /**
     * Get test case
     *
     * Return an instance of TestCase
     *
     * @return TestCase
     */
    protected function getTestCase() : TestCase
    {
        return $this;
    }
}
