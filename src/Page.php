<?php
namespace kennydude\Wiki;

use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use kennydude\Wiki\ImageUrlProcessor;
use kennydude\Wiki\Render\Wiki;
use Symfony\Component\Yaml\Parser;

class Page {
    public function __construct($pagename){
        $this->page_name = str_replace("/", ":", $pagename);

        $x = strstr($this->page_name, ":", true);
        if($x !== FALSE){
            $this->namespace = $x;
        }

        $this->contents = null;
    }

    public function render($response){
        if(stripos($this->contents(), "---") === 0){
            $yaml = substr($this->contents, 4);
            $end = stripos($yaml, "---");
            $yaml = substr($yaml, 0, $end);

            $parser = new Parser();
            $yaml = $parser->parse($yaml);

            $this->contents = substr($this->contents, $end+8);

            if(!$yaml['type']){
                Wiki::render($response, $this, $yaml);
            } else{
                $type = ucwords($yaml['type']);
                call_user_func("kennydude\Wiki\Render\\$type::render", $response, $this, $yaml);
            }

        } else {
          Wiki::render($response, $this, $yaml);
        }
    }

    public function nice_name(){
        return $this->page_name;
    }

    public function link(){
        return "pages/" . $this->page_name;
    }

    public function canRead($req){
        global $config, $user;
        if($this->namespace){
            if($config['namespaces'][$this->namespace]){
                $c = $config['namespaces'][$this->namespace];
                if($c['require']){
                    // Require something
                    $req = $c['require']; // TODO: allow array
                    if(!$user->inGroup($req)){
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public function filename(){
        $p = $this->page_name;
        $p = str_replace(":", "/", $p);
        return "pages/" . $p . ".md";
    }

    public function contents(){
        if(!$this->contents){
            $this->contents = @file_get_contents($this->filename());
        }
        return $this->contents;
    }

    public function get_images_url(){
        return "images/" . ($this->namespace ? $this->namespace . ":" : "");
    }

    public function get_image_url($image){
        return $this->get_images_url() . str_replace("/", ":", $image);
    }

    public function toHTML(){
        $page = $this->contents();

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addInlineProcessor(
            new ImageUrlProcessor(
                $this
            )
        );

        $parser = new DocParser($environment);
        $htmlRenderer = new HtmlRenderer($environment);

        $document = $parser->parse($page);
        return $htmlRenderer->renderBlock($document);
    }

    public function exists(){
        return file_exists($this->filename());
    }

    public static function get_all_pages(){
        $r = array();

        $pages = array_merge(
            glob("pages/*.md"),
            glob("pages/**/*.md"),
            glob("pages/*.markdown"),
            glob("pages/**/*.markdown")
        );
        foreach($pages as $page){
            $r[] = new Page( substr($page, 6, strrpos($page, ".")-6) );
        }

        return $r;
    }

    public static function get_page($name){
        $name = str_replace("%20", " ", $name);
        return new Page($name);
    }

}
