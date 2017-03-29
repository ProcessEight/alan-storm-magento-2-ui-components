<?php
/**
 * @link http://alanstorm.com/magento_2_simplest_xsd_valid_ui_component/
 */
namespace Pulsestorm\SimpleValidUiComponent\Block;

class Example extends \Magento\Framework\View\Element\AbstractBlock
{
    /**
     * Render block.
     * This is a standard block class rendered via the current area’s layout, and needa to extend
     * the Magento\Framework\View\Element\AbstractBlock class. Normally this would be phtml template blocks,
     * but we’re using a block with a hard coded toHtml method here for simplicity’s sake.
     *
     * @return string
     */
    public function toHtml()
    {
        return '<h1>Hello PHP Block Rendered in JS</h1>';
    }
}
