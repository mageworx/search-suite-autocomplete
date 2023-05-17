<?php

namespace MageWorx\SearchSuiteAutocomplete\Block;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Framework\Pricing\Render;

/**
 * Product class
 */
class Product extends AbstractProduct
{
    /**
     * Return HTML block with tier price
     *
     * @param ProductModel $product
     * @param string $priceType
     * @param string $renderZone
     * @param array $arguments
     * @return string
     */
    public function getProductPriceHtml(
        ProductModel $product,
                     $priceType,
                     $renderZone = Render::ZONE_ITEM_LIST,
        array        $arguments = []
    ): string {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }

        $priceRender = $this->getPriceRender();
        $price       = '';

        if ($priceRender) {
            $price = $priceRender->render(
                $priceType,
                $product,
                $arguments
            );
        }

        return $price;
    }

    /**
     * Retrieve price render block
     *
     * @return Render
     */
    protected function getPriceRender(): Render
    {
        return $this->_layout->createBlock(
            'Magento\Framework\Pricing\Render',
            '',
            ['data' => ['price_render_handle' => 'catalog_product_prices']]
        );
    }
}
