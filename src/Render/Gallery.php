<?php
namespace kennydude\Wiki\Render;

class Gallery extends BaseRenderer {
    public static function render($response, $page, $yaml){
        $lines = explode("\n", $page->contents());
        $images = array();
        foreach($lines as $line){
            if(trim($line) == "") continue;

            $images[] = $page->get_image_url($line);
        }

        render($response, "gallery.html", array(
            "images" => $images,
            "name" => $name,
            "title" => $page->nice_name()
        ));
    }
}
