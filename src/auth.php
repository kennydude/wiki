<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class User{
    public function __construct($username){
        global $config;
        $this->username = $username;
        $this->groups = $config['users'][$username]['groups'];
    }
    public function inGroup($group){
        return in_array($group, $this->groups);
    }
}
class Guest{
    public function inGroup($group){
        if($group == "guests"){ return true; }
        return false;
    }
}

if($_COOKIE[session_name()]){
    // Do not start a session unless we have asked for one
    session_start();
    if(!$_SESSION){
        define("LOGGED_IN", false);
        $user = new Guest();
    } else{
        $user = new User($_SESSION['ww_user']['username']);
        define("LOGGED_IN", !!$user);
    }
} else{
    define("LOGGED_IN", false);
    $user = new Guest();
}

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
