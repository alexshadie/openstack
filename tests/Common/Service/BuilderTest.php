<?php

namespace OpenStack\Test\Common\Service;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Event\Emitter;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use OpenStack\Common\Service\Builder;

class BuilderTest extends \PHPUnit_Framework_TestCase
{
    private $builder;
    private $opts;

    function setUp()
    {
        $this->builder = new Builder([]);

        $this->opts = [
            'username' => '1',
            'password' => '2',
            'tenantId' => '3',
            'authUrl' => '4',
            'region' => '5',
            'catalogName' => '6',
            'catalogType' => '7',
        ];
    }

    /**
     * @expectedException \Exception
     */
    public function test_it_throws_exception_if_username_is_missing()
    {
        $this->builder->createService('Compute', 2, []);
    }

    /**
     * @expectedException \Exception
     */
    public function test_it_throws_exception_if_password_is_missing()
    {
        $this->builder->createService('Compute', 2, ['username' => 1]);
    }

    /**
     * @expectedException \Exception
     */
    public function test_it_throws_exception_if_both_tenantId_and_tenantName_is_missing()
    {
        $this->builder->createService('Compute', 2, [
            'username' => 1, 'password' => 2, 'authUrl' => 4, 'region' => 5, 'catalogName' => 6, 'catalogType' => 7,
        ]);
    }

    /**
     * @expectedException \Exception
     */
    public function test_it_throws_exception_if_authUrl_is_missing()
    {
        $this->builder->createService('Compute', 2, ['username' => 1, 'password' => 2, 'tenantId' => 3]);
    }

    /**
     * @expectedException \Exception
     */
    public function test_it_throws_exception_if_region_is_missing()
    {
        $this->builder->createService('Compute', 2, [
            'username' => 1, 'password' => 2, 'tenantId' => 3, 'authUrl' => 4,
        ]);
    }

    /**
     * @expectedException \Exception
     */
    public function test_it_throws_exception_if_catalogName_is_missing()
    {
        $this->builder->createService('Compute', 2, [
            'username' => 1, 'password' => 2, 'tenantId' => 3, 'authUrl' => 4,
        ]);
    }

    /**
     * @expectedException \Exception
     */
    public function test_it_throws_exception_if_catalogType_is_missing()
    {
        $this->builder->createService('Compute', 2, [
            'username' => 1, 'password' => 2, 'tenantId' => 3, 'authUrl' => 4, 'region' => 5, 'catalogName' => 6,
        ]);
    }

    public function test_it_builds_services()
    {
        $response = new Response(200, ['Content-Type' => 'application/json'], Stream::factory(json_encode(['access' => [
            'token' => [
                'issued_at' => '2014-01-30T15:30:58.819584',
                'expires' => '2014-01-30T15:30:58.819584',
                'id' => 'foo',
            ],
            'serviceCatalog' => [
                [
                    'endpoints' => [
                        [
                            'region' => $this->opts['region'],
                            'publicURL' => 'foo.com',
                        ]
                    ],
                    'name' => $this->opts['catalogName'],
                    'type' => $this->opts['catalogType'],
                ]
            ]
        ]])));

        $request = new Request('POST', 'tokens');

        $httpClient = $this->prophesize(ClientInterface::class);
        $httpClient->getEmitter()->willReturn(new Emitter());
        $httpClient->createRequest('POST', 'tokens', [
            'json' => ['auth' => ['passwordCredentials' => ['username' => '1', 'password' => '2'], 'tenantId' => '3']]
        ])->shouldBeCalled()->willReturn($request);
        $httpClient->send($request)->shouldBeCalled()->willReturn($response);

        $this->opts['httpClient'] = $httpClient->reveal();
        $this->opts['debug'] = true;

        $this->assertInstanceOf(
            'OpenStack\Compute\v2\Service',
            $this->builder->createService('Compute', 2, $this->opts)
        );
    }
}
