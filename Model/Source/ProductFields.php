<?php

namespace MageWorx\SearchSuiteAutocomplete\Model\Source;

class ProductFields
{
    const NAME = 'name';

    const SKU = 'sku';

    const IMAGE = 'image';

    const REVIEWS_RATING = 'reviews_rating';

    const SHORT_DESCRIPTION = 'short_description';

    const DESCRIPTION = 'description';

    const PRICE = 'price';

    const ADD_TO_CART = 'add_to_cart';

    const URL = 'url';

    /**
     *
     * @return array
     */
    public function toOptionArray()
    {
        $this->options = [
            ['value' => self::NAME, 'label' => __('Product Name')],
            ['value' => self::SKU, 'label' => __('SKU')],
            ['value' => self::IMAGE, 'label' => __('Product Image')],
            ['value' => self::REVIEWS_RATING, 'label' => __('Reviews Rating')],
            ['value' => self::SHORT_DESCRIPTION, 'label' => __('Short Description')],
            ['value' => self::DESCRIPTION, 'label' => __('Description')],
            ['value' => self::PRICE, 'label' => __('Price')],
            ['value' => self::ADD_TO_CART, 'label' => __('Add to Cart Button')],
        ];

        return $this->options;
    }
}
