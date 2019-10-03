<?php

namespace MageWorx\SearchSuiteAutocomplete\Model;

use Magento\Framework\Controller\Result\JsonFactory;
use MageWorx\SearchSuiteAutocomplete\Api\SearchInterface;

class SearchResult implements SearchInterface
{
    const SEARCH_AUTOCOMPLETE_URL = 'mageworx_searchsuiteautocomplete/ajax/index/?q=';

    protected $searchResult;
    protected $_resultJsonFactory;

    /**
     * SearchResult constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        JsonFactory $resultJsonFactory
    ) {
        $this->_storeManager = $storeManager;
        $this->_resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @param $search
     * @return false|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSearchResult($search)
    {
        $url = $this->_storeManager->getStore()->getBaseUrl() . self::SEARCH_AUTOCOMPLETE_URL . $search;
        $contents = file_get_contents($url);
        $result = json_decode($contents, true);

        return $result;
    }
}
