<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

/**
 * Surreal fluent query
 *
 * @author  EDDYMENS
 * @license MIT (or other licence)
 */
class Surreal
{
    private $url;
    private $db;
    private $nameSpace;
    private $user;
    private $pass;
    private $query;
    private $httpClient;

    /**
     * Constructor
     *
     * @param  string $url DB URL
     * @param  string $user username
     * @param  string $pass password
     * @param  string $db DB
     * @param  string $nameSpace DB namespace
     * @return object
     */
    public function __construct($url, $user, $pass, $db, $nameSpace)
    {
        $this->url = $url;
        $this->db = $db;
        $this->nameSpace = $nameSpace;
        $this->user = $user;
        $this->pass = $pass;
        $this->httpClient = new Client(['base_url' => 'https://api.test.com/']);
    }

    public function requestProcessor($query="", $method="POST", $route="/sql")
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url . $route,
            CURLOPT_RETURNTRANSFER => true,
            // CURLOPT_FAILONERROR => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $query,
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'NS: ' . $this->nameSpace,
                'DB: ' . $this->db,
                'Authorization: Basic ' . base64_encode($this->user . ':' . $this->pass),
            ),
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode >= 400) {
            throw new \Exception($response);
        } else {
            return json_decode($response, true);
        }
    }
}
 
$surreal = new Surreal('http://localhost:8704', 'root', 'password', '', '');

// Runtime::enableCoroutine();

// $client = new SwooleClient('http://localhost', 8704);

// $result = $client->get('/sql');

// var_dump($result);

// var_dump("is this async");
$result = $surreal->requestProcessor(query: "INFO FOR KV;" , method: "POST", route: "/sql");
foreach ($result[0]["result"]["namespaces"] as $key => $value) {
    var_dump($key);
}
