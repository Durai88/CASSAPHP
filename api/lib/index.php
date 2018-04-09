<?php
error_reporting(E_ALL ^ E_STRICT);
require('php-cassandra.php');
$nodes = [// simple way, hostname with port 
    [               // advanced way, array including username, password and socket options
        'host'      => '100.100.100.100',
        'port'      => 9042, //default 9042
        //'username'  => 'cassandra',
        //'password'  => 'cassandra',
     //  'socket'    => [SO_RCVTIMEO => ["sec" => 10, "usec" => 0], //socket transport only
        ],
];

// Create a connection.
$connection = new Cassandra\Connection($nodes, 'tradebees_dev');

//Connect
try
{
    $connection->connect();
}
catch (Cassandra\Exception $e)
{
    echo 'Caught exception: ',  $e->getMessage(), "\n";
    exit;//if connect failed it may be good idea not to continue
}


// Set consistency level for farther requests (default is CONSISTENCY_ONE)
//$connection->setConsistency(Request::CONSISTENCY_QUORUM);

// Run query synchronously.

/*1) yf_product_batch_import_logs
2) yf_product_batch_import_status 
3) yf_product_books
4) yf_product_freight
5) yf_product_logistics
6) yf_product_personalprice
7) yf_product_sales_status
8) yf_product_status
9) yf _product_tag
10) yf_product_translate
11) yf_product_units*/
try
{
    $response = $connection->querySync('SELECT * FROM yf_product_batch_import_logs,yf_product_batch_import_status');
}
catch (Cassandra\Exception $e)
{
}
$rows = $response->fetchAll();  
echo "<pre>";
print_r($rows);