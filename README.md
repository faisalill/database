# Utopia Database

[![Build Status](https://travis-ci.org/utopia-php/database.svg?branch=master)](https://travis-ci.com/utopia-php/database)
![Total Downloads](https://img.shields.io/packagist/dt/utopia-php/database.svg)
[![Discord](https://img.shields.io/discord/564160730845151244?label=discord)](https://appwrite.io/discord)

Utopia framework database library is simple and lite library for managing application persistency using multiple database adapters. This library is aiming to be as simple and easy to learn and use. This library is maintained by the [Appwrite team](https://appwrite.io).

Although this library is part of the [Utopia Framework](https://github.com/utopia-php/framework) project it is dependency free, and can be used as standalone with any other PHP project or framework.

## Getting Started

Install using composer:

```bash
composer require utopia-php/database
```

### Concepts

A list of the utopia/php concepts and their relevant equivalent using the different adapters

- **Database** - An instance of the utopia/database library that abstracts one of the supported adapters and provides a unified API for CRUD operation and queries on a specific schema or isolated scope inside the underlining database.
- **Adapter** - An implementation of an underlying database engine that this library can support - below is a list of [supported adapters](#adapters) and supported capabilities for each Adapter.
- **Collection** - A set of documents stored on the same adapter scope. For SQL-based adapters, this will be equivalent to a table. For a No-SQL adapter, this will equivalent to a native collection.
- **Document** - A simple JSON object that will be stored in one of the utopia/database collections. For SQL-based adapters, this will be equivalent to a row. For a No-SQL adapter, this will equivalent to a native document.
- **Attribute** - A simple document attribute. For SQL-based adapters, this will be equivalent to a column. For a No-SQL adapter, this will equivalent to a native document field.
- **Index** - A simple collection index used to improve the performance of your database queries.
- **Permissions** - Using permissions, you can decide which roles have read, create, update and delete access for a specific document. The special attribute `$permissions` is used to store permission metadata for each document in the collection. A permission role can be any string you want. You can use `Authorization::setRole()` to delegate new roles to your users, once obtained a new role a user would gain read, create, update or delete access to a relevant document.

### Filters

Attribute filters are functions that manipulate attributes before saving them to the database and after retrieving them from the database. You can add filters using the `Database::addFilter($name, $encode, $decode)` where `$name` is the name of the filter that we can add later to attribute `filters` array. `$encode` and `$decode` are the functions used to encode and decode the attribute, respectively. There are also instance-level filters that can only be defined while constructing the `Database` instance. Instance level filters override the static filters if they have the same name.

### Reserved Attributes

- `$id` - the document unique ID, you can set your own custom ID or a random UID will be generated by the library.
- `$createdAt` - the document creation date, this attribute is automatically set when the document is created.
- `$updatedAt` - the document update date, this attribute is automatically set when the document is updated.
- `$collection` - an attribute containing the name of the collection the document is stored in.
- `$permissions` - an attribute containing an array of strings. Each string represent a specific action and role. If your user obtains that role for that action they will have access for this document.

### Attribute Types

The database document interface only supports primitives types (`strings`, `integers`, `floats`, and `booleans`) translated to their native database types for each of the relevant database adapters. Complex types like arrays or objects will be encoded to JSON strings when stored and decoded back when fetched from their adapters.

## Examples

### Setting up different database adapters

**MariaDB:**

```php
require_once __DIR__ . '/vendor/autoload.php';

use PDO;
use Utopia\Database\Database;
use Utopia\Cache\Cache;
use Utopia\Cache\Adapter\Memory;
use Utopia\Database\Adapter\MariaDB;

$dbHost = 'mariadb';
$dbPort = '3306';
$dbUser = 'root';
$dbPass = 'password';
$pdoConfig = [
    PDO::ATTR_TIMEOUT => 3, // Seconds
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => true,
    PDO::ATTR_STRINGIFY_FETCHES => true,
];

$pdo = new PDO("mysql:host={$dbHost};port={$dbPort};charset=utf8mb4", $dbUser, $dbPass, $pdoConfig);

$cache = new Cache(new Memory()); // or use any cache adapter you wish

$database = new Database(new MariaDB($pdo), $cache);
```

**MySQL:**

```php
require_once __DIR__ . '/vendor/autoload.php';

use PDO;
use Utopia\Database\Database;
use Utopia\Cache\Cache;
use Utopia\Cache\Adapter\Memory;
use Utopia\Database\Adapter\MySQL;

$dbHost = 'mysql';
$dbPort = '3306';
$dbUser = 'root';
$dbPass = 'password';
$pdoConfig = [
    PDO::ATTR_TIMEOUT => 3, // Seconds
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => true,
    PDO::ATTR_STRINGIFY_FETCHES => true,
];

$pdo = new PDO("mysql:host={$dbHost};port={$dbPort};charset=utf8mb4", $dbUser, $dbPass, $pdoConfig);

$cache = new Cache(new Memory()); // or use any cache adapter you wish

$database = new Database(new MySql($pdo), $cache);
```

**Postgres:**

```php
require_once __DIR__ . '/vendor/autoload.php';

use PDO;
use Utopia\Database\Database;
use Utopia\Cache\Cache;
use Utopia\Cache\Adapter\Memory;
use Utopia\Database\Adapter\Postgres;

$dbHost = 'postgres';
$dbPort = '5432';
$dbUser = 'root';
$dbPass = 'password';
$pdoConfig = [
    PDO::ATTR_TIMEOUT => 3, // Seconds
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => true,
    PDO::ATTR_STRINGIFY_FETCHES => true,
];

$pdo = new PDO("pgsql:host={$dbHost};port={$dbPort};charset=utf8mb4", $dbUser, $dbPass, $pdoConfig);

$cache = new Cache(new Memory()); // or use any cache adapter you wish

$database = new Database(new Postgres($pdo), $cache);
```

**SQLite:**

```php
require_once __DIR__ . '/vendor/autoload.php';

use PDO;
use Utopia\Database\Database;
use Utopia\Cache\Cache;
use Utopia\Cache\Adapter\Memory;
use Utopia\Database\Adapter\SQLite;

$dbPath = '/path/to/database.sqlite';
$pdoConfig = [
    PDO::ATTR_TIMEOUT => 3, // Seconds
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => true,
    PDO::ATTR_STRINGIFY_FETCHES => true,
];

$pdo = new PDO("{$dbPath}", $pdoConfig);

$cache = new Cache(new Memory()); // or use any cache adapter you wish

$database = new Database(new SQLite($pdo), $cache);
```

**MongoDB:**

```php
require_once __DIR__ . '/vendor/autoload.php';

use Utopia\Database\Database;
use Utopia\Cache\Cache;
use Utopia\Cache\Adapter\Memory;
use Utopia\Database\Adapter\Mongo;
use Utopia\Mongo\Client; // from utopia-php/mongo

$dbHost = 'mongo';
$dbPort = 27017; 
$dbUser = 'root';
$dbPass = 'password';
$dbName = 'dbName';

$mongoClient = new Client($dbName, $dbHost, $dbPort, $dbUser, $dbPass, true);

$cache = new Cache(new Memory()); // or use any cache adapter you wish

$database = new Database(new Mongo($client), $cache);
```

<br>

> Following methods are available for all database adapters.

<br>

**Database Methods:**

```php

// Get namespace
$database->getNamespace();

// Sets namespace that prefixes all collection names
$database->setNamespace(
    namespace: 'namespace'
);

// Get default database
$database->getDefaultDatabase();

// Sets default database
$database->setDefaultDatabase(
    name: 'dbName'
);

// Creates a new database. 
// Uses default database as the name.
$database->create();

// Returns an array of all databases
$database->list();

// Delete database
$database->delete(
    name: 'mydb'
);

// Ping database it returns true if the database is alive
$database->ping();

// Check if database exists
$database->exists(
    database: 'mydb'
); 

// Check if collection exists
$database->exists(
    database: 'mydb',
    collection: 'users'
); 

// Listen to events

// Event Types
Database::EVENT_ALL
Database::EVENT_DATABASE_CREATE,
Database::EVENT_DATABASE_LIST,
Database::EVENT_COLLECTION_CREATE,
Database::EVENT_COLLECTION_LIST,
Database::EVENT_COLLECTION_READ,
Database::EVENT_ATTRIBUTE_CREATE,
Database::EVENT_ATTRIBUTE_UPDATE,
Database::EVENT_INDEX_CREATE,
Database::EVENT_DOCUMENT_CREATE,
Database::EVENT_DOCUMENT_UPDATE,
Database::EVENT_DOCUMENT_READ,
Database::EVENT_DOCUMENT_FIND,
Database::EVENT_DOCUMENT_FIND,
Database::EVENT_DOCUMENT_COUNT,
Database::EVENT_DOCUMENT_SUM,
Database::EVENT_DOCUMENT_INCREASE,
Database::EVENT_DOCUMENT_DECREASE,
Database::EVENT_INDEX_DELETE,
Database::EVENT_DOCUMENT_DELETE,
Database::EVENT_ATTRIBUTE_DELETE,
Database::EVENT_COLLECTION_DELETE,
Database::EVENT_DATABASE_DELETE,

$database->on(
    string: Database::EVENT_ALL, 
    callable: function($event, $data) {
        // Do something
    }
);

// Get Database Adapter
$database->getAdapter();

// Get List of keywords that cannot be used
$database->getKeywords();
```

**Collection Methods:**

```php
// Creates two new collection named '$namespace_$collectionName' with attribute names '_id', '_uid', '_createdAt', '_updatedAt', '_permissions' 
// The second collection is named '$namespace_$collectionName_perms' with attribute names '_id', '_type', '_permission', '_document'
$database->createCollection(
    name: 'users'
);

// Create collection with attributes and indexes
$attributes = [
     new Document([
         '$id' => ID::unique(),
         '$permissions' => [
          Permission::read(Role::any()),
          Permission::update(Role::any()),
          Permission::delete(Role::any())
         ],
         'name' => 'Jhon', 
         'age'  =>  20
     ]),
     new Document([
         '$id' => ID::unique(),
         '$permissions' => [
          Permission::read(Role::any()),
          Permission::update(Role::any()),
          Permission::delete(Role::any())
         ],
         'name' => 'Doe', 
         'age'  =>  34
     ]),
]

$indexes = [
     new Document([
         '$id' => ID::unique(),
         'type' => Database::INDEX_KEY,
         'attributes' => ['name'],
         'lengths' => [256],
         'orders' => ['ASC'],
        ]),
     new Document([
         '$id' => ID::unique(),
         'type' => Database::INDEX_KEY,
         'attributes' => ['name', 'age'],
         'lengths' => [128, 128],
         'orders' => ['ASC'],
        ])
];

$database->createCollection(
    name: 'users', 
    attributes: $attributes, 
    indexes: $indexes
);

// Update Collection Permissions
$database->updateCollection(
    id: 'users',
    permissions: [
        Permission::read(Role::any()),
        Permission::update(Role::any()),
        Permission::delete(Role::any())
    ],
    documentSecurity: true
);

// Get Collection
$database->getCollection(
    id: 'users'
);

// List Collections
$database->listCollections(
    limit: 25, 
    offset: 0
);

// Deletes the two collections named 'namespace_$collectionName' and 'namespace_$collectionName_perms'
$database->deleteCollection(
    id: 'users'
);

// Delete cached documents of a collection
$database->deleteCachedCollection(
    collection: 'users'
);
```

**Attribute Methods:**

```php
// Data types
Database::VAR_STRING      
Database::VAR_INTEGER
Database::VAR_FLOAT
Database::VAR_BOOLEAN  
Database::VAR_DATETIME


// Creates a new attribute named '$attributeName' in the '$namespace_$collectionName' collection.
$database->createAttribute(
    collection: 'movies',
    id: 'name',
    type:  Database::VAR_STRING,
    size: 128, 
    required: true
);

// New attribute with optional parameters
$database->createAttribute(
    collection: 'movies', 
    id: 'genres',
    type: Database::VAR_STRING, 
    size: 128, 
    required: true, 
    default: null, 
    signed: true, 
    array: false, 
    format: null, 
    formatOptions: [], 
    filters: []
);

// Updates the attribute named '$attributeName' in the '$namespace_$collectionName' collection.
$database-> updateAttribute(
    collection: 'movies', 
    id: 'genres',
    type: Database::VAR_STRING, 
    size: 128, 
    required: true, 
    default: null, 
    signed: true, 
    array: false, 
    format: null, 
    formatOptions: [], 
    filters: []
);

// Update the required status of an attribute
$database->updateAttributeRequired(
    collection: 'movies', 
    id: 'genres',
    required: true
);

// Update the attribute format
$database->updateAttributeFormat(
    collection: 'movies', 
    id: 'genres',
    format: null,
);

// Update the attribute format options
$database->updateAttributeFormatOptions(
    collection: 'movies', 
    id: 'genres',
    formatOptions: []
);

// Update the attribute filters
$database->updateAttributeFilters(
    collection: 'movies', 
    id: 'genres',
    filters: []
);

// Update the default value of an attribute
$database->updateAttributeDefault(
    collection: 'movies', 
    id: 'genres',
    default: 'sci-fi'
);

// Check if attribute can be added to a collection
$collection = $database->getCollection('movies');

$attribute = new Document([
    '$id' => ID::unique(),
    'type' => Database::VAR_INTEGER,
    'size' => 256,
    'required' => true,
    'default' => null,
    'signed' => true,
    'array' => false,
    'filters' => [],
]);

$database->checkAttribute(
    collection: $collection,
    attribute: $attribute
);

// Get Adapter attribute limit
$database->getLimitForAttributes(); // if 0 then no limit

// Get Adapter index limit
$database->getLimitForIndexes(); 

// Renames the attribute from old to new in the '$namespace_$collectionName' collection.
$database->renameAttribute(
    collection: 'movies',
    old: 'genres', 
    new: 'genres2'
);

// Deletes the attribute in the '$namespace_$collectionName' collection.
$database->deleteAttribute(
    collection: 'movies', 
    id: 'genres'
);
```

**Index Methods:**

```php
// Index types
Database::INDEX_KEY,                 
Database::INDEX_FULLTEXT
Database::INDEX_UNIQUE
Database::INDEX_SPATIAL
Database::INDEX_ARRAY

// Insertion Order                                 
Database::ORDER_ASC
Database::ORDER_DESC
   

// Creates a new index named '$indexName' in the '$namespace_$collectionName' collection.
// Note: The size for the index will be taken from the size of the attribute
$database->createIndex(
    collection: 'movies', 
    id: 'index1', Database::INDEX_KEY, 
    attributes: ['name', 'genres'], 
    lengths: [128,128], 
    orders: [Database::ORDER_ASC, Database::ORDER_DESC]
);

// Rename index from old to new in the '$namespace_$collectionName' collection.
$database->renameIndex(
    collection: 'movies', 
    old: 'index1', 
    new: 'index2'
);

// Deletes the index in the '$namespace_$collectionName' collection.
$database->deleteIndex(
    collection: 'movies', 
    id: 'index2'
);
``` 

**Relationship Methods:**

```php
// Relationship types
Database::RELATION_ONE_TO_ONE
Database::RELATION_ONE_TO_MANY
Database::RELATION_MANY_TO_ONE
Database::RELATION_MANY_TO_MANY

// Creates a relationship between the two collections with the default reference attributes
$database->createRelationship(
    collection: 'movies', 
    relatedCollection: 'users', 
    Database::RELATION_ONE_TO_ONE,, 
    twoWay: true
);


// Create a relationship with custom reference attributes
$database->createRelationship(
    collection: 'movies', 
    relatedCollection: 'users', 
    Database::RELATION_ONE_TO_ONE, 
    twoWay: true, 
    id: 'movies_id', 
    twoWayKey: 'users_id'
); 

// Relationship onDelete types
Database::RELATION_MUTATE_CASCADE, 
Database::RELATION_MUTATE_SET_NULL,
Database::RELATION_MUTATE_RESTRICT,

// Update the relationship with the default reference attributes
$database->updateRelationship(
    collection: 'movies', 
    id: 'users', 
    onDelete: Database::RELATION_MUTATE_CASCADE
); 

// Update the relationship with custom reference attributes
$database->updateRelationship(
    collection: 'movies', 
    id: 'users', 
    onDelete: Database::RELATION_MUTATE_CASCADE, 
    newKey: 'movies_id', 
    newTwoWayKey: 'users_id', 
    twoWay: true
);

// Delete the relationship with the default or custom reference attributes
$database->deleteRelationship(
    collection: 'movies', 
    id: 'users'
);
```

**Document Methods:**

```php
use Utopia\Database\Document;             
use Utopia\Database\Helpers\ID;
use Utopia\Database\Helpers\Permission;
use Utopia\Database\Helpers\Role;

// Id helpers
ID::unique(padding: 12),        // Creates an id of length 7 + padding
ID::custom(id: 'my_user_3235')  

// Role helpers
Role::any(),
Role::user(ID::unique())    

// Permission helpers
Permission::read(Role::any()),
Permission::create(Role::user(ID::unique())),
Permission::update(Role::user(ID::unique(padding: 23))),
Permission::delete(Role::user(ID::custom(id: 'my_user_3235'))

// To create a document
$document = new Document([
    '$permissions' => [
        Permission::read(Role::any()),
        Permission::create(Role::user(ID::custom('1x'))),
        Permission::update(Role::user(ID::unique(12))),
        Permission::delete(Role::user($customId)),
    ],
    'name' => 'Captain Marvel',
    'director' => 'Anna Boden & Ryan Fleck',
    'year' => 2019,
    'price' => 25.99,
    'active' => true,
    'genres' => ['science fiction', 'action', 'comics'],
]);

$document = $database->createDocument(
    collection: 'movies', 
    document: $document
);

// Get which collection a document belongs to
$document->getCollection();

// Get document id
$document->getId();

// Check whether document in empty
$document->isEmpty();

// Increase an attribute in a document 
$database->increaseDocumentAttribute(
    collection: 'movies', 
    id: $document->getId(),
    attribute: 'name', 
    value: 24,
    max: 100
);

// Decrease an attribute in a document
$database->decreaseDocumentAttribute(
    collection: 'movies', 
    id: $document->getId(),
    attribute: 'name', 
    value: 24, 
    min: 100
);

// Update the value of an attribute in a document

// Set types
Document::SET_TYPE_ASSIGN,
Document::SET_TYPE_APPEND,
Document::SET_TYPE_PREPEND

$document->setAttribute(key: 'name', 'Chris Smoove')
         ->setAttribute(key: 'age', 33, Document::SET_TYPE_ASSIGN);

$database->updateDocument(
    collection: 'users', 
    id: $document->getId(), 
    document: $document
);         

// Update the permissions of a document
$document->setAttribute('$permissions', Permission::read(Role::any()), Document::SET_TYPE_APPEND)
         ->setAttribute('$permissions', Permission::create(Role::any()), Document::SET_TYPE_APPEND)
         ->setAttribute('$permissions', Permission::update(Role::any()), Document::SET_TYPE_APPEND)
         ->setAttribute('$permissions', Permission::delete(Role::any()), Document::SET_TYPE_APPEND)

$database->updateDocument(
    collection: 'users', 
    id: $document->getId(), 
    document: $document
);

// Info regarding who has permission to read, create, update and delete a document
$document->getRead(); // returns an array of roles that have permission to read the document
$document->getCreate(); // returns an array of roles that have permission to create the document
$document->getUpdate(); // returns an array of roles that have permission to update the document
$document->getDelete(); // returns an array of roles that have permission to delete the document

// Get document with all attributes
$database->getDocument(
    collection: 'movies', 
    id: $document->getId()
); 

// Get document with a sub-set of attributes
$database->getDocument(
    collection: 'movies', 
    id: $document->getId(), 
    [Query::select(['name', 'director', 'year'])]
);

// Find documents 

// Query Types
Query::equal(attribute: "...", values: ["...", "..."]),
Query::notEqual(attribute: "...", value: "..."),
Query::lessThan(attribute: "...", value: 100),
Query::lessThanEqual(attribute: "...", value: 1000),
Query::greaterThan(attribute: "...", value: 1000),
Query::greaterThanEqual(attribute: "...", value: ...),  
Query::contains(attribute: "...", values: ["...", "..."]),
Query::between(attribute: "...", start: 100, end: 1000),
Query::search(attribute: "...", value: "..."),
Query::select(attributes: ["...", "..."]),
Query::orderDesc(attribute: "..."),
Query::orderAsc(attribute: "..."),
Query::isNull(attribute: "..."),
Query::isNotNull(attribute: "..."),
Query::startsWith(attribute: "...", value: "..."),
Query::endsWith(attribute: "...", value: "..."),
Query::limit(value: 35),
Query::offset(value: 0),

$database->find(
    collection: 'movies', 
    queries:  [
        Query::equal(attribute: 'name', values: ['Captain Marvel']),
        Query::notEqual(attribute: 'year', values: [2019])
    ], 
    timeout: 1);  //timeout is optional

// Find a document 
$database->findOne(
    collection: 'movies', 
    queries:  [
        Query::equal(attribute: 'name', values: ['Captain Marvel']),
        Query::lessThan(attribute: 'year', value: 2019)
    ]
);  

// Get count of documents 
$database->count(
    collection: 'movies', 
    queries:  [
        Query::equal(attribute: 'name', values: ['Captain Marvel']),
        Query::greaterThan(attribute: 'year', value: 2019)
    ], 
    max: 1000); // max is optional
);

// Get the sum of an attribute from all the documents
$database->sum(
    collection: 'movies', 
    attribute: 'price', 
    queries:  [
        Query::greaterThan(attribute: 'year', value: 2019)
    ],
    max: 0 // max = 0 means no limit
); 

// Encode Document
$collection = $database->getCollection('movies');

$document = $database->getDocument(
    collection: 'movies', 
    id: $document->getId()
);

$database->encode(
    collection: $collection, 
    document: $document
);

// Decode Document
$database->decode(
    collection: $collection, 
    document: $document
);

// Delete a document
$database->deleteDocument(
    collection: 'movies', 
    id: $document->getId()
);

// Delete a cached document
$database->deleteCachedDocument(
    collection: 'movies', 
    id: $document->getId()
);

```

### Adapters

Below is a list of supported adapters, and their compatibly tested versions alongside a list of supported features and relevant limits.

| Adapter  | Status | Version |
| -------- | ------ | ------- |
| MariaDB  | ✅     | 10.5    |
| MySQL    | ✅     | 8.0     |
| Postgres | 🛠     | 13.0    |
| MongoDB  | ✅     | 5.0     |
| SQLlite  | ✅     | 3.38    |

` ✅  - supported, 🛠  - work in progress`

## Limitations (to be completed per adapter)

- ID max size can be 255 bytes
- ID can only contain [^A-Za-z0-9] and symbols `_` `-`
- Document max size is x bytes
- Collection can have a max of x attributes
- Collection can have a max of x indexes
- Index value max size is x bytes. Values over x bytes are truncated

## System Requirements

Utopia Framework requires PHP 8.0 or later. We recommend using the latest PHP version whenever possible.

## Tests

To run tests, you first need to bring up the example Docker stack with the following command:

```bash
docker compose up -d --build
```

To run all unit tests, use the following Docker command:

```bash
docker compose exec tests vendor/bin/phpunit --configuration phpunit.xml tests
```

To run tests for a single file, use the following Docker command structure:

```bash
docker compose exec tests vendor/bin/phpunit --configuration phpunit.xml tests/Database/[FILE_PATH]
```

To run static code analysis, use the following Psalm command:

```bash
docker compose exec tests vendor/bin/psalm --show-info=true
```

### Load testing

Three commands have been added to `bin/` to fill, index, and query the DB to test changes:

- `bin/load` invokes `bin/tasks/load.php`
- `bin/index` invokes `bin/tasks/index.php`
- `bin/query` invokes `bin/tasks/query.php`

To test your DB changes under load:

#### Load the database

```bash
docker compose exec tests bin/load --adapter=[adapter] --limit=[limit] [--name=[name]]

# [adapter]: either 'mongodb' or 'mariadb', no quotes
# [limit]: integer of total documents to generate
# [name]: (optional) name for new database
```

#### Create indexes

```bash
docker compose exec tests bin/index --adapter=[adapter] --name=[name]

# [adapter]: either 'mongodb' or 'mariadb', no quotes
# [name]: name of filled database by bin/load
```

#### Run Query Suite

```bash
docker compose exec tests bin/query --adapter=[adapter] --limit=[limit] --name=[name]

# [adapter]: either 'mongodb' or 'mariadb', no quotes
# [limit]: integer of query limit (default 25)
# [name]: name of filled database by bin/load
```

#### Visualize Query Results

```bash
docker compose exec tests bin/compare
```

Navigate to `localhost:8708` to visualize query results.

## Copyright and license

The MIT License (MIT) [http://www.opensource.org/licenses/mit-license.php](http://www.opensource.org/licenses/mit-license.php)