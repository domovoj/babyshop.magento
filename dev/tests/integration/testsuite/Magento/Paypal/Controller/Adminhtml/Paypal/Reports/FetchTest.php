<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Paypal\Controller\Adminhtml\Paypal\Reports;

/**
 * @magentoAppArea adminhtml
 */
class FetchTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    public function setUp()
    {
        $this->resource = 'Magento_Paypal::fetch';
        $this->uri = 'backend/paypal/paypal_reports/fetch';
        parent::setUp();
    }
}
