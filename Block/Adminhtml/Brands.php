<?php
/**
 * Copyright © 2015 EvatoErp. All rights reserved.
 */
namespace Evato\Globis\Block\Adminhtml;

class Brands extends \Magento\Backend\Block\Template
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'brands';
        $this->_headerText = __('Brands');
        //$this->_addButtonLabel = __('Add New Item');
        parent::_construct();
    }
}
