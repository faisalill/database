<?php

namespace Utopia\Tests\Adapter;

use PHPUnit\Framework\TestCase;
use Utopia\Database\Adapter\SurrealDB;

class SurrealDBTest extends TestCase {
    
    const DATABASE_URL = "http://surrealdb:8000";
    const USERNAME = "root";
    const PASSWORD = "password";
    public $database; 

    public function setUp(): void {
        $this->database = new SurrealDB(self::DATABASE_URL, self::USERNAME, self::PASSWORD);
    }

    public function testPing(): void
    {
       $results = $this->database->ping();
       $this->assertEquals(true, $results);
    }

}

?>

