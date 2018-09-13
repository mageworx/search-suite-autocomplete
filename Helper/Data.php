<?php

namespace MageWorx\SearchSuiteAutocomplete\Helper;

use Magento\Store\Model\ScopeInterface;

/**
 * Search Suite Autocomplete config data helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * XML config path search delay
     */
    const XML_PATH_SEARCH_DELAY = 'mageworx_searchsuite/searchsuiteautocomplete_main/search_delay';

    /**
     * XML config path autocomplete fields
     */
    const XML_PATH_AUTOCOMPLETE_FIELDS = 'mageworx_searchsuite/searchsuiteautocomplete_main/autocomplete_fields';

    /**
     * XML config path suggest results number
     */
    const XML_PATH_SUGGESTED_RESULT_NUMBER = 'mageworx_searchsuite/searchsuiteautocomplete_main/suggested_result_number';

    /**
     * XML config path product results number
     */
    const XML_PATH_PRODUCT_RESULT_NUMBER = 'mageworx_searchsuite/searchsuiteautocomplete_main/product_result_number';

    /**
     * XML config path product result fields
     */
    const XML_PATH_PRODUCT_RESULT_FIELDS = 'mageworx_searchsuite/searchsuiteautocomplete_main/product_result_fields';

    /**
     * Retrieve search delay
     *
     * @param int|null $storeId
     * @return int
     */
    public function getSearchDelay($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_SEARCH_DELAY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve comma-separated autocomplete fields
     *
     * @param int|null $storeId
     * @return string
     */
    public function getAutocompleteFields($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AUTOCOMPLETE_FIELDS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve list of autocomplete fields
     *
     * @param int|null $storeId
     * @return array
     */
    public function getAutocompleteFieldsAsArray($storeId = null)
    {
        return explode(',', $this->getAutocompleteFields($storeId));
    }

    /**
     * Retrieve suggest results number
     *
     * @param int|null $storeId
     * @return int
     */
    public function getSuggestedResultNumber($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_SUGGESTED_RESULT_NUMBER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve product results number
     *
     * @param int|null $storeId
     * @return int
     */
    public function getProductResultNumber($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_RESULT_NUMBER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve comma-separated product result fields
     *
     * @param int|null $storeId
     * @return string
     */
    public function getProductResultFields($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_RESULT_FIELDS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve list of product result fields
     *
     * @param int|null $storeId
     * @return array
     */
    public function getProductResultFieldsAsArray($storeId = null)
    {
        return explode(',', $this->getProductResultFields($storeId));
    }
}
