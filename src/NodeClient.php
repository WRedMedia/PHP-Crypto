<?php

namespace TronTool;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class NodeClient
{
    protected $client;

    function __construct($uri)
    {
        $this->client = Http::baseUrl($uri);
    }

    static function mainNet()
    {
        return new self('http://127.0.0.1:8090');
    }

    static function testNet()
    {
        return new self('https://api.shasta.trongrid.io');
    }

    /**
     * @throws Exception
     */
    function post($api, $payload = [])
    {
//        $opts = [
//            'json' => $payload
//        ];

        try {
            $rsp = $this->client->post($api, $payload);
            if ($rsp->successful()) {
                return $this->handle($rsp);
            }
        } catch (ConnectionException $e) {
            throw new Exception($e->getMessage());
        }
        return false;
    }

    /**
     * @throws Exception
     */
    function get($api, $query = [])
    {
//        $opts = [
//            'query' => $query
//        ];
        try {
            $rsp = $this->client->get($api, $query);
            if ($rsp->successful()) {
                return $this->handle($rsp);
            }
        } catch (ConnectionException $e) {
            throw new Exception($e->getMessage());
        }
        return false;
    }

    function handle($rsp)
    {
        return json_decode($rsp->getBody());
    }

    function version()
    {
        return '1.0.0';
    }
}
