<?php

namespace Vendor\Test\Console\Command;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupDefaultValues extends Command
{
    private const PRODUCT_SKU = '24-MB01';
    private const CONFIG_PATH_DEPENDENT_PRODUCT_ID = 'product_dependency/general/product_id';

    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ScopeConfigInterface       $scopeConfig,
        private readonly UrlInterface               $urlBuilder,
        private readonly State                      $appState
    )
    {
        parent::__construct();
    }

    /**
     * Initialization
     */
    protected function configure(): void
    {
        $this->setName('vendor:test:setup')
            ->setDescription('Setup default values. Works only with simple data');
    }

    /**
     *
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->appState->setAreaCode('frontend');
        } catch (LocalizedException) {

        }

        try {
            $product = $this->productRepository->get(self::PRODUCT_SKU);
        } catch (NoSuchEntityException) {
            echo "Sample data product not found.\n";
            return Command::FAILURE;
        }

        $product->setCustomAttribute('has_dependency', true);
        try {
            $this->productRepository->save($product);
        } catch (LocalizedException $exception) {
            echo $exception->getMessage();
            return Command::FAILURE;
        }
        $productName = $product->getName();
        $productUrl = $this->urlBuilder->getUrl('catalog/product/view', ['id' => $product->getId()]);
        $dependentProductId = (int)$this->scopeConfig->getValue(self::CONFIG_PATH_DEPENDENT_PRODUCT_ID);
        if ($dependentProductId) {
            try {
                $dependentProductUrl = $this->urlBuilder->getUrl('catalog/product/view', ['id' => $dependentProductId]);
                echo "Product '{$productName}' (SKU: " . self::PRODUCT_SKU . ") 'has_dependency' set.\n";
                echo "URL: {$productUrl}\n";
                echo "Dependent Product URL: {$dependentProductUrl}\n";
            } catch (NoSuchEntityException) {
                echo "Dependent product not found.\n";
            }
        } else {
            echo "No dependent product ID found.\n";
        }

        return Command::SUCCESS;
    }
}
