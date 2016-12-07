<?php
/**
 * Copyright © 2015 EvatoErp. All rights reserved.
 */

namespace Evato\Globis\Controller\Adminhtml\Categories;

class Index extends \Evato\Globis\Controller\Adminhtml\Categories
{	
	public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Evato_Globis::globis');
        $resultPage->getConfig()->getTitle()->prepend(__('Globis Categories'));
		$resultPage->addContent(
            $resultPage->getLayout()->createBlock("Evato\Globis\Block\Adminhtml\Categories")->setTemplate("categories/index.phtml")
        );
        return $resultPage;
    }
}
