<?php

namespace De\Idrinth\MiniMindmap\Result;

use DOMDocument;
use DOMNode;

class Xml extends Base
{
    public function __construct()
    {
        parent::__construct('text/xml; charset=utf-8');
    }
    private function appendToParent(DOMDocument $doc, DOMNode $parent, array $children): void
    {
        foreach ($children as $child) {
            $node = $doc->createElement('node');
            $node->setAttribute('uuid', $child['uuid']);
            $text = $doc->createElement('text');
            $textContent = $doc->createTextNode($child['text']);
            $text->appendChild($textContent);
            $node->appendChild($text);
            if (isset($child['description'])) {
                $description = $doc->createElement('description');
                $descriptionContent = $doc->createTextNode($child['description']);
                $description->appendChild($descriptionContent);
                $node->appendChild($description);
            }
            if (isset($child['image'])) {
                $image = $doc->createElement('image');
                $imageContent = $doc->createTextNode($child['image']);
                $image->appendChild($imageContent);
                $node->appendChild($image);
            }
            if (isset($child['children'])) {
                $children = $doc->createElement('children');
                $this->appendToParent($doc, $children, $child['children']);
                $node->appendChild($children);
            }
            $parent->appendChild($node);
        }
    }
    function send(): void
    {
        $this->sendHeaders();
        $doc = new DOMDocument();
        $mindmap = $doc->createElement('mindmap');
        $doc->appendChild($mindmap);
        $this->appendToParent($doc, $mindmap, [$this->data]);
        echo $doc->saveXML();
    }
}
