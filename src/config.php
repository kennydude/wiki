<?php
namespace kennydude\Wiki;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class Config{

    public static function get(){
        $yaml = new Parser();

        try {
            $config = $yaml->parse(@file_get_contents('config.yml'));
            if(!$config){ throw new ParseException("Did not parse"); }
        } catch (ParseException $e) {
            if($twig){
                echo $twig->render("503.html", array(
                    "error" => $e,
                    "base" => BASE_DIR
                ));
            } else{
                echo $e;
                echo "\n!!! Could not parse config.yml !!!\n";
            }
            exit(-1);
        }

        return $config;
    }

}
