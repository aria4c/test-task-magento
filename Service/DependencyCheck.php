<?php

declare(strict_types=1);

namespace Vendor\Test\Service;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Session\Proxy as CheckoutSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\Phrase;

class DependencyCheck
{
    private const XML_CONFIG_PATH_DEPENDED_PRODUCT = 'product_dependency/general/product_id';
    private const XML_CONFIG_PATH_ENABLED = 'product_dependency/general/enabled';

    public function __construct(
        private readonly CheckoutSession            $checkoutSession,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly UrlInterface               $urlBuilder,
        private readonly ScopeConfigInterface       $scopeConfig
    )
    {
    }

    /**
     * Check is module enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_CONFIG_PATH_ENABLED);
    }

    /**
     * Get depended product ID
     *
     * @return int|null
     */
    public function getDependentProductId(): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_CONFIG_PATH_DEPENDED_PRODUCT);
    }

    /**
     * Check if dependency message should be displayed.
     *
     * @return bool
     */
    public function displayDependencyMessage(): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $dependedProductId = $this->getDependentProductId();
        if (!$dependedProductId) {
            return false;
        }

        $items = $this->checkoutSession->getQuote()->getAllVisibleItems();

        $hasDependencyProduct = false;
        $hasRequiredProduct = false;

        foreach ($items as $item) {
            $productId = $item->getProductId();

            try {
                $product = $this->productRepository->getById($productId);

                if ($product->getId() === $this->getDependentProductId()) {
                    $hasRequiredProduct = true;
                }

                if ((bool)$product->getData('has_dependency')) {
                    $hasDependencyProduct = true;
                }
            } catch (NoSuchEntityException) {
                continue;
            }
        }

        return $hasDependencyProduct && !$hasRequiredProduct;
    }

    /**
     * Get dependency message
     *
     * @param bool $escapeHtml
     * @return Phrase|null
     */
    public function getDependencyMessage(bool $escapeHtml = true): ?Phrase
    {
        if ($this->displayDependencyMessage()) {
            try {
                $dependedProductId = $this->getDependentProductId();
                $product = $this->productRepository->getById($dependedProductId);
                $productName = $product->getName();
                $productUrl = $this->urlBuilder->getUrl('catalog/product/view', ['id' => $dependedProductId]);

                return $escapeHtml ? __(
                    'Product <a href="%1">%2</a> is required for order.',
                    $productUrl,
                    $productName
                ) : __(
                    'Product %1 is required for order.',
                    $productName);
            } catch (NoSuchEntityException) {
                return __('Required product is missing for this purchase.');
            }
        }

        return null;
    }
}
