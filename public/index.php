<?php
require __DIR__ . '/../vendor/autoload.php';

/*##############################Working Example 1#################################*/

/*if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}


session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register middleware
require __DIR__ . '/../src/middleware.php';

// Register routes
require __DIR__ . '/../src/routes.php';

// Run app
$app->run();*/


/*##############################Working Example 2 - Simple#################################*/

/*use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;
$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
      $chkarr = array('mynam'=>'aasas','aaaa','ssss');
    $response->getBody()->write("Hello, $name");
$newResponse = $response->withJson($chkarr);
    return $newResponse;
});
$app->run();*/

/*##############################Working Example 3 - Multiple#################################*/

/*$app = new \Slim\App();

$app->get('/{sss}', function ($request, $response) {
		  $sss = $request->getAttribute('sss');
    return $response->getBody()->write("Hello World $sss");
});

$app->group('/utils', function () use ($app) {
    $app->get('/date', function ($request, $response) {
        return $response->getBody()->write(date('Y-m-d H:i:s'));
    });
    $app->get('/time', function ($request, $response) {
        return $response->getBody()->write(time());
    });
})->add(function ($request, $response, $next) {
   // $response->getBody()->write('It is now ');
    $response = $next($request, $response);

    //$response->getBody()->write('. Enjoy!');
    $reArr =array('011' => '222' , '11' => '22' );
    $response = $response->withJson($reArr);

    return $response;
});
*/

/*##############################Working Example 3 - Db Connection#################################*/


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$app = new \Slim\App;



/*
$app->get('/users', function (Request $request, Response $response) {
	require_once 'lib/mysql.php';
	$getBooks = getUsers();
    $name = $request->getAttribute('name');
      $chkarr = array('mynam'=>'aasas','aaaa','ssss');
    $response->getBody()->write("Hello, $name");
$newResponse = $response->withJson($getBooks);
    return $newResponse;
});*/




//$app->add(new \Slim\HttpCache\Cache('public', 86400));

//CACHE***********************
$container = new \Slim\Container;
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



// Add middleware to the application
$app = new \Slim\App($container);

			$c = $app->getContainer();
$app->add(new \Slim\HttpCache\Cache('public', 86400));

//CACHE***********************


$app->group('/users', function () use ($app) {
   
    $app->get('/show', function ($request, $response) {
			require_once 'lib/mysql.php';
			$getBooks = getUsers();
			/*echo "<pre>";
			print_r($getBooks);*/
			$name = $request->getAttribute('name');
			$chkarr = array('mynam'=>'aasas','aaaa','ssss');
			$response->getBody()->write("Hello, $name");
			$newResponse = $response->withJson($getBooks);
			 $newResponse = $this->cache->withEtag($newResponse, 'abc');//CACHE***********************
			 $newResponse = $this->cache->withLastModified($newResponse, time() - 3600);//CACHE***********************
			 /* return $newResponse->withHeader(
        'Content-Type',
        'application/json'
    );*/

			  		return $newResponse;
    });

    
    $app->post('/up/{id}', function ($request, $response) {
    	    $postDate = $request->getParsedBody();
       		require_once 'lib/mysql.php';			
			$id 	= $request->getAttribute('id');
			$upsId  = updateUserbyID($id,$postDate);
			$newResponse = $response->withJson($upsId);
			 $newResponse = $this->cache->withEtag($newResponse, 'abc');//CACHE***********************
			 $newResponse = $this->cache->withLastModified($newResponse, time() - 3600);//CACHE***********************
			  return $newResponse->withHeader(
        'Content-Type',
        'application/json'
    );
			//return $newResponse;
			
    });
});






$container['errorHandler'] = function ($container) {
    return function ($request, $response, $exception) use ($container) {
        return $container['response']->withStatus(500)
                             ->withHeader('Content-Type', 'text/html')
                             ->write('Something went wrong!');
    };
};



// Run application
$app->run();


