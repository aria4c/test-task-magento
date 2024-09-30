<?php
declare(strict_types=1);

namespace Vendor\Test\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Phrase;
use Vendor\Test\Service\DependencyCheck;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\ActionInterface;

readonly class CheckoutPageDependencyCheckObserver implements ObserverInterface
{
    public function __construct(
        private DependencyCheck   $dependencyCheck,
        private RedirectInterface $redirect,
        private ActionFlag        $actionFlag
    )
    {
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        if (!$this->dependencyCheck->isEnabled()) {
            return;
        }
        $message = $this->dependencyCheck->getDependencyMessage();
        if ($message instanceof Phrase) {
            $action = $observer->getControllerAction();
            $this->redirect->redirect($action->getResponse(), 'checkout/cart');
            $this->actionFlag->set('', ActionInterface::FLAG_NO_DISPATCH, true);
        }
    }
}
