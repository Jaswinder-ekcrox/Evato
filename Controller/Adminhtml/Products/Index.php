<?php
/**
 * Copyright © 2015 EvatoErp. All rights reserved.
 */

namespace Evato\Globis\Controller\Adminhtml\Products;

class Index extends \Evato\Globis\Controller\Adminhtml\Products
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
        $resultPage->getConfig()->getTitle()->prepend(__('Globis Products'));
        return $resultPage;
    }
}
