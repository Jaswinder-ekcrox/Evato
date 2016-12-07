<?php
/**
 * Copyright © 2015 EvatoErp. All rights reserved.
 */
namespace Evato\Globis\Block\Adminhtml;

class Customers extends \Magento\Backend\Block\Template
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'customers';
        $this->_headerText = __('Customers');
        parent::_construct();
    }
}
