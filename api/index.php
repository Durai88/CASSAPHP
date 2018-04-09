<?php
require __DIR__ . '/../vendor/autoload.php';
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//CACHE***********************
$container = new \Slim\Container;
$app = new \Slim\App($container);
$c = $app->getContainer();
$app->add(new \Slim\HttpCache\Cache('public', 36000));
$container['cache'] = function () {
    return new \Slim\HttpCache\CacheProvider();
};

$container['errorHandler'] = function ($container) {
    return function ($request, $response, $exception) use ($container) {
        return $container['response']->withStatus(500)
                             ->withHeader('Content-Type', 'text/html')
                             ->write('Something went wrong!');
    };
};

//Product Search **************************************************************

$app->group('/product_search/{auth}', function () use ($app) {
   
    $app->post('/getproduct/keyword_{keyword}/country_{isbn}/price_{price_start}_{price_end}', function ($request, $response) {
        $auth = $request->getAttribute('auth');
        //Search Things ---- http://172.16.51.161/newslim/api/product_search/U5GQZ17o3mHi3CmQhoL1qAh7MBvuMl2U6CMAHww9QbE3EQU3/getproduct/keyword_1/country_0/price_0_0 ----

         $keyword       = str_replace('%20',' ',addslashes($request->getAttribute('keyword')));
      // return "select id,title ,abstract ,author,category_code,combo_book_id ,illustrator ,combo_book_status ,series ,pubdate ,edition ,editor , is_copyright ,isbn ,language_code ,on_sell ,publisher_code ,quantity,user_id   from yf_product_books where solr_query = 'abstract:".$keyword." author:".$keyword." title:".$keyword." isbn:".$keyword." category_code:".$keyword." illustrator:".$keyword." pubdate:".$keyword."  series:".$keyword."'";
        $country       = str_replace('%20',' ',addslashes($request->getAttribute('country')));
        $price_start   = str_replace(''   ,' ',addslashes($request->getAttribute('price_start')));
        $price_end     = str_replace(''   ,' ',addslashes($request->getAttribute('price_end')));


        require_once 'lib/cassa.php';
        $comAPi      = new comAPi;
        $cassaClass  = new cassClass;
       // $rtnVal = $comAPi->chekAuthCode($auth);
        
       // if($rtnVal == ''){
        $rtnVal = $comAPi->getUsers();
      //           $rtnVal = $cassaClass->SearchProduct($keyword,$country,$price_start,$price_end);
                
       // }
       $newResponse = $response->withJson($rtnVal);
       //$newResponse = $this->cache->withEtag($newResponse, 'abc');//CACHE***********************
      // $newResponse = $this->cache->withLastModified($newResponse, time() - 36000);//CACHE***********************      
       return $newResponse;
    });

    

});





// Run application
$app->run();


