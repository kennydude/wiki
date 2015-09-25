<?php
namespace kennydude\Wiki\Render;

use kennydude\Wiki\Render\BaseRenderer;

class Sms extends BaseRenderer {
    public static function render($response, $page, $yaml){
        $lines = array();
        $chat = explode("\n", $page->contents());

        // Parse "sms" chat thing
        $classes = array();
        $upto = 0;
        $name = "";
        foreach($chat as $line){
            if(trim($line) == "") continue;

            $from = substr($line, 0, stripos($line, ":"));
            $msg = substr($line, stripos($line, ":")+1);

            if(!$classes[$from]){
                $upto += 1;
                $classes[$from] = $upto;
                if($upto == 1){
                    $name = $from;
                }
            }
            $class = $classes[$from];

            $matches = array();
            if(preg_match('/!\[((?:\[[^\]]*\]|[^\[\]]|\](?=[^\[]*\]))*)\]\(\s*<?([\s\S]*?)>?(?:\s+[\'"]([\s\S]*?)[\'"])?\s*\)/', $msg, $matches) > 0){
                $lines[] = array(
                    "from" => $from,
                    "image" => $page->get_image_url($matches[2]),
                    "photo" => $page->get_image_url($yaml[$from]),
                    "class" => $class
                );
            } else{
                $lines[] = array(
                    "from" => $from,
                    "message" => $msg,
                    "photo" => $page->get_image_url($yaml[$from]),
                    "class" => $class
                );
            }
        }

        // Get styles
        $styles = array();
        $s = array_merge(
            glob("assets/sms/*.css"),
            glob("assets/sms/*.less")
        );
        foreach($s as $style){
            $styles[] = substr($style, 11, strrpos($style, ".")-11);
        }

        render($response, "sms.html", array(
            "chat" => $lines,
            "styles" => $styles,
            "name" => $name,
            "title" => $page->nice_name()
        ));
    }
}
