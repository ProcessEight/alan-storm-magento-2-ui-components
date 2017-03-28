<?php
namespace Pulsestorm\SimpleUiComponent\Component;

class Simple extends \Magento\Ui\Component\AbstractComponent
{
    const NAME = 'pulsestorm_simple';

    /**
     * Returns component name
     *
     * @return string
     */
    public function getComponentName()
    {
        return static::NAME;
    }

    /**
     * Provides data for the component to use
     *
     * @return string[]
     */
    public function getDataSourceData()
    {
        return ['data' => $this->getContext()->getDataProvider()->getData()];
    }

    /**
     * Returns even more data
     *
     * @return string
     */
    public function getEvenMoreData()
    {
        return 'Even more data';
    }
}
