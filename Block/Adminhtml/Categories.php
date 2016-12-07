<?php
/**
 * Copyright © 2015 EvatoErp. All rights reserved.
 */
namespace Evato\Globis\Block\Adminhtml;

class Categories extends \Magento\Backend\Block\Template
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'Categories';
        $this->_headerText = __('Categories');
        parent::_construct();
    }
}
