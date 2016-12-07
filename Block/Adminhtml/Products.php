<?php
/**
 * Copyright © 2015 EvatoErp. All rights reserved.
 */
namespace Evato\Globis\Block\Adminhtml;

class Products extends \Magento\Backend\Block\Template
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'products';
        $this->_headerText = __('Products');
        parent::_construct();
    }
}
