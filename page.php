<?php
require 'vendor/autoload.php';
error_reporting(E_ALL ^ E_NOTICE);

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use kennydude\Wiki\Page;
use kennydude\Wiki\Auth\User;
use kennydude\Wiki\Config;

require("src/util.php");

define("BASE_DIR", str_repeat("../", count(explode("/", $_GET['page']))-1));

// Setup Twig
$loader = new Twig_Loader_Filesystem("views/");
$twig = new Twig_Environment($loader, array(
    #"cache" => "caches/twig",
));

// Load Router
$container = new League\Container\Container();
$container->add('Symfony\Component\HttpFoundation\Request', Request::createFromGlobals());
$router = new League\Route\RouteCollection($container);

// Simple global render function
function render($response, $template, $data){
    global $twig, $user;

    $data['base'] = BASE_DIR;

    $sidebar = new Page("Sidebar");
    $data['sidebar'] = $sidebar->toHTML();

    $data['user'] = $user;

    $response->setContent($twig->render($template, $data));
}

$config = Config::get();
$user = User::get();

$router->addRoute('GET', '/assets/{asset:.+}.css', function(Request $request, Response $response, $args){
    $parser = new Less_Parser();
    $parser->parseFile( 'assets/' . $args['asset'] . '.less' );
    $css = $parser->getCss();
    $response->setContent($css);
    $response->headers->set('Content-Type', 'text/css');

    return $response;
});

$router->addRoute('GET', '/pages/', function(Request $request, Response $response){

    $pages = Page::get_all_pages();
    $r = array();
    foreach($pages as $page){
        if($page->canRead($request) == true){
            $r[] = $page;
        }
    }

    render($response, "pagelist.html", array( "pages" => $r ));
    return $response;
});

$router->addRoute('GET', '/pages/{name}', function (Request $request, Response $response, $args) {
    $page = Page::get_page($args['name']);

    if(!$page->exists() || !$page->canRead($request)){
        render($response, "nopage.html", array());
        $response->setStatusCode(404);
        return $response;
    }

    $page->render($response);
    return $response;
});

$image_exts = array("png", "jpg", "jpeg", "gif");

$router->addRoute('GET', '/images/{key}', function(Request $request, Response $response, $args){
    global $image_exts;
    $key = str_replace(":", "/", $args['key']);
    $key = str_replace("%20", " ", $key);

    $filename = "pages/" . $key;
    $ext = rstrstr($filename, ".");
    if(!in_array($ext, $image_exts)){
        render($response, "nopage.html", array());
        $response->setStatusCode(404);
        return $response;
    }

    if(file_exists($filename)){
        $response->headers->set('Content-Type', 'image/' . $ext);
        $response->setContent(file_get_contents($filename));
    } else{
        render($response, "nopage.html", array());
        $response->setStatusCode(404);
    }
    return $response;
});

$router->addRoute('GET', '/login', function(Request $request, Response $response){
    render($response, "login.html", array());
    return $response;
});

$router->addRoute('POST', '/login', function(Request $request, Response $response){
    global $config;
    $error = null;

    if($config['users'][$request->request->get("username")]){
        $user = $config['users'][$request->request->get("username")];
        if(password_verify($request->request->get("password"), $user['password'])){
            session_start();
            $_SESSION['ww_user'] = array(
                "username" => $request->request->get("username")
            );
            $response->setStatusCode(302);
            $response->headers->set("Location", "pages/Home");
        } else{
            $error = "Invalid password";
        }
    } else{
        $error = "Invalid username";
    }

    render($response, "login.html", array( "error" => $error ));
    return $response;
});

$router->addRoute('GET', '/', function(Request $request, Response $response){
    $response->setStatusCode(302);
    $response->headers->set("Location", "pages/Home");
    return $response;
});

$dispatcher = $router->getDispatcher();
$request = Request::createFromGlobals();

$response = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());

$response->send();
