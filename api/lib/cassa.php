<?php
error_reporting(0);
require('common.php');
require('php-cassandra.php');

class cassClass{

function connect_cassa_db(){
        $nodes = [
           [               // advanced way, array including username, password and socket options
        'host'      => '100.100.100.100',
        'port'      => 9042, //default 9042
        'username'  => 'XXX',
        'password'  => 'WWW',
        'socket'    => [SO_RCVTIMEO => ["sec" => 10, "usec" => 0], //socket transport only
        ],
    ], 

    [               // advanced way, array including username, password and socket options
        'host'      => '100.100.100.101',
        'port'      => 9042, //default 9042
        'username'  => 'XXX',
        'password'  => 'WWW',
        'socket'    => [SO_RCVTIMEO => ["sec" => 10, "usec" => 0], //socket transport only
        ],
    ], 

    [               // advanced way, array including username, password and socket options
        'host'      => '100.100.100.102',
        'port'      => 9042, //default 9042
        'username'  => 'XXX',
        'password'  => 'WWW',
        'socket'    => [SO_RCVTIMEO => ["sec" => 10, "usec" => 0], //socket transport only
        ],
    ], 

        ];

        // Create a connection.
        $connection = new Cassandra\Connection($nodes, 'tradebees_dev');
        try
        {
            $connection->connect();
        }
        catch (Cassandra\Exception $e)
        {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            exit;//if connect failed it may be good idea not to continue
        }
        return $connection;
}



    function SearchProduct($keyword,$country,$price_start,$price_end){

             $connection = $this->connect_cassa_db();   
           // echo "SELECT title,isbn,id FROM yf_product_books WHERE title > '$keyword' ALLOW FILTERING";

            try
            {
                // $connection->setConsistency(Request::CONSISTENCY_ONE); 
                //echo "select count(*) from yf_product_books"; echo "<br>";
                $keyword = $keyword;
                // $keyword = '';
               //echo "select id,cid_code,user_id,title,imagepath,abstract ,author,category_code,combo_book_id ,illustrator ,combo_book_status ,series ,pubdate ,edition ,editor , is_copyright ,isbn ,language_code ,on_sell ,publisher_code ,quantity,user_id   from yf_product_books where solr_query = 'abstract:*".$keyword."* OR author:*".$keyword."* OR title:*".$keyword."* OR isbn:*".$keyword."* OR category_code:*".$keyword."* OR illustrator:*".$keyword."* OR pubdate:*".$keyword."* OR  series:*".$keyword."*'  LIMIT 100000";
                if(is_numeric($keyword)){
                    $response = $connection->querySync("select id,cid_code,user_id,title,imagepath,abstract ,author,category_code,combo_book_id ,illustrator ,combo_book_status ,series ,pubdate ,edition ,editor , is_copyright ,isbn ,language_code ,on_sell ,publisher_code ,quantity,user_id   from yf_product_books where solr_query = 'abstract:*".$keyword."* OR author:*".$keyword."* OR title:*".$keyword."* OR isbn:*".$keyword."* OR category_code:*".$keyword."* OR illustrator:*".$keyword."* OR pubdate:*".$keyword."* OR series:*".$keyword."*'  LIMIT 100000");
                }else{
                     $response = $connection->querySync("select id,cid_code,user_id,title,imagepath,abstract ,author,category_code,combo_book_id ,illustrator ,combo_book_status ,series ,pubdate ,edition ,editor , is_copyright ,isbn ,language_code ,on_sell ,publisher_code ,quantity,user_id   from yf_product_books where solr_query = 'abstract:".$keyword." OR author:".$keyword." OR title:".$keyword." OR isbn:".$keyword." OR category_code:".$keyword." OR illustrator:".$keyword." OR pubdate:".$keyword." OR series:".$keyword."'  LIMIT 100000");
                }
                

                //$response = $connection->querySync("select * from tradebees_dev.yf_product_books where solr_query = 'author:c'");

            }
            catch (Cassandra\Exception $e)
            {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
                exit;//if connect failed it may be good idea not to continue
            }
            $rows = $response->fetchAll(); 
            return $rows;

    }

}







//Connect



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
9) yf _product_tag WHERE AS.id=trans.id AS 
10) yf_product_translate AS trans
11) yf_product_units*/
