<?php
namespace kennydude\Wiki\Render;

use kennydude\Wiki\Render\BaseRenderer;

class Wiki extends BaseRenderer {
    public static function render($response, $page, $yaml){
        render($response, "page.html", array(
            "page" => $page->toHTML(),
            "title" => $page->nice_name()
        ));
    }
}
