<?php
// Command line tool for simplewiki
require "vendor/autoload.php";
error_reporting(E_ALL ^ E_NOTICE);

use Symfony\Component\Yaml\Dumper;
use kennydude\Wiki\Config;

if (php_sapi_name() != "cli") {
    exit("E: c.php must be ran via the command line");
}
echo "simplewiki\n";
echo "© kennydude 2014\n";

if(file_exists("config.yml")){
    $config = Config::get();
} else{
    echo "> config.yml does not exist!\n";
    $config = array(
      "users" => array()
    );
}

function save_config(){
    global $config;
    $dumper = new Dumper();
    $yaml = $dumper->dump($config, 9);
    file_put_contents("config.yml", $yaml);
    echo "> Configuration updated ✓\n";
}

function get_line(){
    $l = fgets(STDIN);
    return substr($l, 0, strlen($l) - 1);
}

if(count($argv) < 2){
  $argv = array("", "");
}

function new_user(){
    global $config;
    echo "Add new user\n";

    echo "Username: ";
    $username = get_line();
    echo "** Password is not hidden! **\n";
    echo "Password: ";
    $password = get_line();

    if(!$config['users']){
        $config['users'] = array();
    }
    $config['users'][$username] = array(
        "password" => password_hash($password, PASSWORD_DEFAULT)
    );

    save_config();
}

switch($argv[1]){
    case "createuser":
        new_user();
        break;
    case "install":
        echo "Welcome to installer";
        echo "Do you wish to install? (Y) ";
        $yes = get_line();
        if($yes == "Y"){
            echo "Installing...";
            mkdir("pages");
            file_put_contents("pages/Sidebar.md", "* [List of all pages](pages/)");
            file_put_contents("pages/Home.md", "# Home\nWelcome to your new wiki");
            new_user();
        }
        break;
    default:
        echo "Nothing to do\n";
        break;
}
