<?php

namespace OpenStack\Test\Identity\v2\Models;

use OpenStack\Identity\v2\Models\Endpoint;
use OpenStack\Test\TestCase;

class EndpointTest extends TestCase
{
    private $endpoint;

    public function setUp()
    {
        parent::setUp();

        $this->endpoint = new Endpoint($this->client->reveal());
    }

    public function test_it_supports_internal_urls()
    {
        $url = 'https://internal-openstack.org:5000/v2.0/foo';

        $this->endpoint->populateFromArray(['internalURL' => $url]);

        $this->assertEquals($url, $this->endpoint->getUrl('internalURL'));
    }

    public function test_it_supports_admin_urls()
    {
        $url = 'https://admin-openstack.org:5000/v2.0/foo';

        $this->endpoint->populateFromArray(['adminURL' => $url]);

        $this->assertEquals($url, $this->endpoint->getUrl('adminURL'));
    }
} 