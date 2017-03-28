<?php
namespace Pulsestorm\SimpleUiComponent\Model;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Returns data to be used by component
     *
     * @return string[]
     */
    public function getData()
    {
        return [ 'foo' => 'bar' ];
    }
}
