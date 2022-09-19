<?php

namespace TronTool;

use Exception;

class TronKit
{
    public $api;
    public $credential;

    function __construct($tronApi, $credential = null)
    {
        $this->api = $tronApi;
        $this->credential = $credential;

        //new ExceptionHandler();
    }

    function setCredential($credential)
    {
        $this->credential = $credential;
    }

    /**
     * @throws Exception
     */
    function getCredential()
    {
        if (is_null($this->credential)) {
            throw new Exception('Credential not set.');
        }
        return $this->credential;
    }

    /**
     * @throws Exception
     */
    function getLatestBlocks($limit)
    {
        $block = $this->api->getBlockByLatestNum($limit);
        if ($block[0]->block_header->raw_data->number)
            return $block[0]->block_header->raw_data->number;

        return false;
    }

    /**
     * @throws Exception
     */
    function sendTrx($to, $amount): object
    {
        $credential = $this->getCredential();
        $from = $credential->address()->base58();

        $tx = $this->api->createTransaction($to, $amount, $from);
        $signedTx = $credential->signTx($tx);
        $ret = $this->api->broadcastTransaction($signedTx);
        return (object)[
            'txid' => $signedTx->txID,
            'result' => $ret->result
        ];
    }

    function broadcast($tx)
    {
        return $this->api->broadcastTransaction($tx);
    }

    function getTrxBalance($address)
    {
        return $this->api->getBalance($address);
    }

    /**
     * @throws Exception
     */
    function contract($abi): Contract
    {
        $credential = $this->getCredential();
        return new Contract($this->api, $abi, $credential);
    }

    /**
     * @throws Exception
     */
    function trc20($address): Trc20
    {
        $credential = $this->getCredential();
        $inst = new Trc20($this->api, $credential);
        return $inst->at($address);
    }
}
