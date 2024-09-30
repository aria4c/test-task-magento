<?php

namespace Vendor\Test\Plugin;

use Magento\Checkout\CustomerData\Cart as MagentoCart;
use Vendor\Test\Service\DependencyCheck;

class CustomerDataCartPlugin
{

    public function __construct(private DependencyCheck $dependencyCheck)
    {
    }

    /**
     * Add dependency message to cart section
     *
     * @param MagentoCart $subject
     * @param array $result
     * @return array
     */
    public function afterGetSectionData(MagentoCart $subject, array $result): array
    {
        if (!$this->dependencyCheck->isEnabled()) {
            return $result;
        }

        $result['dependency_message_cart'] = $this->dependencyCheck->getDependencyMessage();
        return $result;
    }
}
