<?php
/**
 * Copyright © 2015 EvatoErp. All rights reserved.
 */

namespace Evato\Globis\Controller\Adminhtml\Customers;

class Index extends \Evato\Globis\Controller\Adminhtml\Customers
{
    /**
     * Items list.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Evato_Globis::globis');
        $resultPage->getConfig()->getTitle()->prepend(__('Globis Customers'));
        return $resultPage;
    }
}
