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
     * @param string $search
     * @return mixed|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSearchResult($search)
    {
        $uri = $this->_storeManager->getStore()->getBaseUrl() . self::SEARCH_AUTOCOMPLETE_URL . $search;
        $request = new \Zend\Http\Request();
        $request->setUri($uri);
        $request->setMethod(\Zend\Http\Request::METHOD_GET);

        $client = new \Zend\Http\Client();
        $response = $client->send($request);
        $result = json_decode($response->getBody(), true);

        return $result;
    }
}
