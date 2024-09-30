<?php

declare(strict_types=1);

namespace Vendor\Test\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Vendor\Test\Service\DependencyCheck;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

readonly class PlaceOrderDependencyCheckObserver implements ObserverInterface
{
    public function __construct(
        private DependencyCheck $dependencyCheck
    )
    {
    }

    /**
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        if (!$this->dependencyCheck->isEnabled()) {
            return;
        }
        $message = $this->dependencyCheck->getDependencyMessage(escapeHtml: false);
        if ($message instanceof Phrase) {
            throw new LocalizedException($message);
        }
    }
}
