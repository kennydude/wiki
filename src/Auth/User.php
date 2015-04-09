<?php
namespace kennydude\Wiki\Auth;

class User{
    public function __construct($username){
        global $config;
        $this->username = $username;
        $this->groups = $config['users'][$username]['groups'];
    }
    public function inGroup($group){
        return in_array($group, $this->groups);
    }

    public static function get(){
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
        return $user;
    }
}
