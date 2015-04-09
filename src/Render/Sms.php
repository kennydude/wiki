<?php
namespace kennydude\Wiki\Render;

use kennydude\Wiki\Render\BaseRenderer;

class Sms extends BaseRenderer {
    public static function render($response, $page, $yaml){
        $lines = array();
        $chat = explode("\n", $page->contents());

        // Parse "sms" chat thing
        foreach($chat as $line){
            if(trim($line) == "") continue;

            $from = substr($line, 0, stripos($line, ":"));
            $msg = substr($line, stripos($line, ":")+1);
            $lines[] = array(
                "from" => $from,
                "message" => $msg,
                "photo" => $page->get_image_url($yaml[$from])
            );
        }

        // Get styles
        $styles = array();
        foreach(glob("assets/sms/*.css") as $style){
            $styles[] = substr($style, 11, strrpos($style, ".")-11);
        }

        render($response, "sms.html", array(
            "chat" => $lines,
            "styles" => $styles,
            "title" => $page->nice_name()
        ));
    }
}
