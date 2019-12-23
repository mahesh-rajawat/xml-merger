<?php
/**
 * Webkul Software.
 *
 * @package   Webkul_CodeGenerator
 * @author    Mahesh Singh
 */

/**
 * XmlGenerator class
 */
class XmlGenerator {

    /**
     * Add new node to parent node
     *
     * @param SimpleXMLElement $parentNode
     * @param string $childNode
     * @param null|array $nodeValue
     * @param array $attributes
     * @param bool $idAttribute
     * @return Element
     */
    public function addXmlNode(
        $parentNode,
        $childNode,
        $nodeValue = null,
        $attributes = [],
        $idAttribute = false,
        $isUniqueNode = false
    ) {
        if (!$parentNode instanceof \SimpleXMLElement){
            $parentNode = new \SimpleXMLElement($parentNode);
        }
        if ($isUniqueNode) {
            $removeNode = $parentNode->xpath("//{$childNode}");
            if (isset($removeNode[0])) {
                $this->removeNode($removeNode[0]);
            }
        }
        if (is_array($nodeValue)) {
            if ($idAttribute) {
                $tempParent = simplexml_load_string($parentNode->asXml());
                $parentName = $parentNode->getName();
                $matchedNode = $tempParent->xpath("//{$childNode}[@{$idAttribute}='{$attributes[$idAttribute]}']");
                $newNode = $parentNode->addChild($childNode);
                if (isset($matchedNode[0]) && $matchedNode[0] instanceof \SimpleXMLElement) {
                    $this->replaceChild($newNode, $matchedNode[0]);
                }
            } else {
                $newNode = $parentNode->addChild($childNode);
            }
            foreach ($nodeValue as $nodeKey => $value) {
                $this->addXmlNode($newNode, $nodeKey, $value);
            }
        } else {
            if ($idAttribute) {
                $parentName = $parentNode->getName();
                $matchedNode = $parentNode->xpath("//{$childNode}[@{$idAttribute}='{$attributes[$idAttribute]}']");
                $newNode = $parentNode->addChild($childNode, $nodeValue?:'');
                if (isset($matchedNode[0]) && $matchedNode[0] instanceof \SimpleXMLElement) {
                    $this->replaceChild($newNode, $matchedNode[0]);
                }
            } else {
                $newNode = $parentNode->addChild($childNode, $nodeValue?:'');
            }
        }
        
        if (!empty($attributes)) {
            foreach ($attributes as $attribute => $value) {
                $newNode->addAttribute($attribute, $value);
            }
        }
        return $newNode;
    }

    /**
     * Remove xml node
     *
     * @param Element $removeNode
     * @return void
     */
    public function removeNode($removeNode)
    {
        $node = dom_import_simplexml($removeNode);
        $node->parentNode->removeChild($node); 
        return $this; 
    } 

    /**
     * replace a child Element with another Element
     * @param SimpleXMLElement $newChild 
     * @param SimpleXMLElement $oldChild
     * @return $this
     */
    public function replaceChild(\SimpleXMLElement &$newChild, \SimpleXMLElement $oldChild)
    {
        list($oldChild, $_newChild) = $this->getSameTypeDomNodes($oldChild, $newChild);
        $oldChild->parentNode->replaceChild($_newChild, $oldChild);
    }

    /**
     * Utility method to get two dom elements
     * And ensure that the second is part of the same document than the first given.
     * @param SimpleXMLElement $node1
     * @param SimpleXMLElement $node2
     * @return array
     */
    public function getSameTypeDomNodes(\SimpleXMLElement $newChild, \SimpleXMLElement $oldChild)
    {
        $newChild = dom_import_simplexml($newChild);
        $oldChild = dom_import_simplexml($oldChild);
        if(! $newChild->ownerDocument->isSameNode($oldChild->ownerDocument) ) {
            $oldChild = $newChild->ownerDocument->importNode($oldChild, true);
        }
        return [$newChild, $oldChild];
    }

    /**
     * Format xml string
     *
     * @param string $content
     * @return string
     */
    public function formatXml($content)
    {
        $dom = new \DOMDocument;
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($content);
        return $dom->saveXML();
    }
}
