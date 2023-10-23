<?php

namespace Utopia\Database\Adapter;

use Utopia\Database\Adapter\Mongo;
use Utopia\Database\Exception as DatabaseException;
use Utopia\Database\Adapter;

class SurrealDB extends Adapter{
    
    // Methods
    protected const METHOD_GET = 'GET';
    protected const METHOD_POST = 'POST';

    // Routes
    protected const ROUTE_SQL = '/sql';
    protected const ROUTE_HEALTH = '/health';

    /**
     * @var string
     */
    protected string $username;   
    
    /**
     * @var string
     */
    protected string $password;    

    /**
     * @var string
     */
    protected string $databaseUrl;   
  
    /**
     * Constructor
     *
     * @param  string $databaseUrl Database URL
     * @param  string $username Username
     * @param  string $password Password
     * @return object
     */
    public function __construct(string $databaseUrl, string $username, string $password)
    {
        $this->databaseUrl = $databaseUrl;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Ping Database
     *
     * @return bool
     * @throws Exception
     * @throws DatabaseException
     */
    public function ping(): bool
    {
        $response = $this->executeQuery(method: self::METHOD_GET, route: self::ROUTE_HEALTH); 
        return true;
    }

    /**
     * Create Database
     *
     * @param string $name
     * @return bool
     * @throws DatabaseException
     */
    public function create(string $name): bool
    {
        $name = $this->filter($name);

        $query = "INFO FOR KV;";
        $response = $this->executeQuery($query, self::METHOD_POST, self::ROUTE_SQL);

        // Create database if it does not exist
        if (!\array_key_exists($name, $response[0]["result"]["namespaces"])){
            $query = "DEFINE NAMESPACE $name;";
            $response = $this->executeQuery($query, self::METHOD_POST, self::ROUTE_SQL);

            $query = "USE NS $name;
                DEFINE DATABASE $name;";
            $response = $this->executeQuery($query, self::METHOD_POST, self::ROUTE_SQL);

            return true;
        }
    }

    /**
     * Check if database exists
     * Optionally check if collection exists in database
     *
     * @param string $database database name
     * @param string|null $collection (optional) collection name
     *
     * @return bool
     * @throws DatabaseException
     */
    public function exists(string $database, string $collection = null): bool
    {
        $query = "INFO FOR KV;";
        $response = $this->executeQuery($query, self::METHOD_POST, self::ROUTE_SQL);
        if (\array_key_exists($database, $response[0]["result"]["namespaces"])){
            if (!\is_null($collection)){
                $query = "USE NS $database DB $database; INFO FOR DB;";
                $response = $this->executeQuery($query, self::METHOD_POST, self::ROUTE_SQL);
                if (\array_key_exists($collection, $response[1]["result"]["tables"])){
                    return true;
                }
            }
            return true;
        }
    }
    
    /**
     * List Databases
     *
     * @return array<Document>
     * @throws DatabaseException
     */
    public function list(): array
    {
        $query = "INFO FOR KV;";
        $response = $this->executeQuery($query, self::METHOD_POST, self::ROUTE_SQL);
        $list = [];
        foreach ($response[0]["result"]["namespaces"] as $key => $value) {
            $list[] = $key;
        }
        return $list;
    }

    /**
     * Delete Database
     *
     * @param string $name
     *
     * @return bool
     * @throws Exception
     */
    public function delete(string $name): bool
    {
        $query = "REMOVE NAMESPACE $name;";
        return true;
    }

    /**
     * Query
     * 
     * Execute SurrealQL query
     * 
     * @param string $query 
     * @param string $method
     * @param string $route
     * @return array
     */
    private function executeQuery(string $query = "", string $method, string $route)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->databaseUrl . $route,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $query,
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Authorization: Basic ' . base64_encode($this->username . ':' . $this->password),
            ),
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode >= 400) {
            throw new DatabaseException($httpCode . " ". $response);
        } else {
            return json_decode($response, true);
        }

    }
}

?>