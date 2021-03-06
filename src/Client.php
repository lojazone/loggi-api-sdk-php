<?php

namespace Lojazone\Loggi;

use GuzzleHttp\Exception\ClientException;

/**
 * Class Client
 * @package Lojazone\Loggi
 */
abstract class Client
{
    /** @var \GuzzleHttp\Client $client */
    protected $client;


    /** @var EndPoints $endpoint */
    protected $endpoint;


    /** @var */
    protected $api_key;


    /** @var */
    protected $email;


    /** @var array|string[] */
    private $header_authorization = [];

    public $lat;

    public $long;

    /**
     * Client constructor.
     * @param null $email
     * @param null $api_key
     */
    public function __construct($email = null, $api_key = null)
    {
        $this->endpoint = new EndPoints();
        if (!is_null($api_key) && !is_null($email)) {
            $this->api_key = $api_key;
            $this->email = $email;
            $this->header_authorization = [
                'Authorization' => "ApiKey {$this->email}:{$this->api_key}"
            ];
        }
        $this->client = new \GuzzleHttp\Client([
            'headers' => [
                    'Content-Type' => 'application/json',
                ] + $this->header_authorization
        ]);
    }

    /**
     * @param $endpoint_name
     * @param $query
     * @return mixed
     * @throws \Exception
     */
    public function request($endpoint_name, $query)
    {
        try {
            $request = $this->client->post($this->endpoint->$endpoint_name, [
                'body' => json_encode(['query' => $query])
            ]);
            return \GuzzleHttp\json_decode($request->getBody());
        } catch (ClientException $clientException) {
            throw new \Exception($clientException->getMessage());
        }
    }

    /**
     * @param $postal_code
     * @param $api_key
     * @return mixed
     * @throws \Exception
     */
    public function getLocation($postal_code, $api_key)
    {
        try {
            $request = $this->client->get("https://maps.googleapis.com/maps/api/geocode/json?address={$postal_code}&key={$api_key}");
            $result = json_decode($request->getBody());
            if ($result->status === "OK") {
                return $result;
            }
            return false;
        } catch (ClientException $clientException) {
            throw new \Exception($clientException->getMessage());
        }
    }




}