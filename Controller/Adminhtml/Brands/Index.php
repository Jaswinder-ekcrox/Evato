<?php
/**
 * Copyright © 2015 EvatoErp. All rights reserved.
 */

namespace Evato\Globis\Controller\Adminhtml\Brands;

class Index extends \Evato\Globis\Controller\Adminhtml\Brands
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
        $resultPage->getConfig()->getTitle()->prepend(__('Evato Brands'));
        //$resultPage->addBreadcrumb(__('EvatoErp'), __('EvatoErp'));
        //$resultPage->addBreadcrumb(__('Images'), __('Images'));
       /* $resultPage->addContent(
            $resultPage->getLayout()->createBlock("Evato\Globis\Block\Adminhtml\Brands")->setTemplate("brands/index.phtml")
        );*/
        return $resultPage;
    }
}
