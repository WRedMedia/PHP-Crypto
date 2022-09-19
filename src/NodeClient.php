<?php

namespace BitcoinTool;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class NodeClient
{
    protected $client;

    static function mainNet()
    {
        return new self('http://127.0.0.1:8332');
    }

    function __construct($uri)
    {
        $this->client = Http::baseUrl($uri);
    }

    /**
     * @throws Exception
     */
    function post($api, $payload = [])
    {
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
}
