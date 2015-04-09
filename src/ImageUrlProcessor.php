<?php
namespace kennydude\Wiki;

use League\CommonMark\Inline\Processor\InlineProcessorInterface;
use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\Delimiter\DelimiterStack;
use League\CommonMark\Util\ArrayCollection;
use League\CommonMark\Inline\Element\Image;

class ImageUrlProcessor implements InlineProcessorInterface {
    private $page;

    public function __construct($page) {
        $this->page = $page;
    }

    public function processInlines(ArrayCollection $inlines, DelimiterStack $delimiterStack, Delimiter $stackBottom = null) {
        foreach ($inlines as $inline) {
            if (!($inline instanceof Image)) {
                continue;
            }

            if ($this->isRelative($inline->getUrl())) {
                $inline->setUrl($this->page->get_image_url($inline->getUrl()));
            }
        }
    }

    private function isRelative($url) {
        if(stripos($url, "/") === false) return true;

        if(stripos($url, "://") < stripos($url, "/")){
            return true;
        }

        return false;
    }
}
