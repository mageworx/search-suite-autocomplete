<?php

namespace MageWorx\SearchSuiteAutocomplete\Model\Search;

use Magento\Store\Model\StoreManagerInterface;
use \MageWorx\SearchSuiteAutocomplete\Helper\Data as HelperData;
use \Magento\Search\Helper\Data as SearchHelper;
use \Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use \Magento\Framework\ObjectManagerInterface as ObjectManager;
use \Magento\Search\Model\QueryFactory;
use \MageWorx\SearchSuiteAutocomplete\Model\Source\AutocompleteFields;
use \MageWorx\SearchSuiteAutocomplete\Model\Source\ProductFields;
use \Magento\Review\Model\ResourceModel\Review\SummaryFactory;

/**
 * Product model. Return product data used in search autocomplete
 */
class Product implements \MageWorx\SearchSuiteAutocomplete\Model\SearchInterface
{
    /**
     * @var \MageWorx\SearchSuiteAutocomplete\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Search\Helper\Data
     */
    protected $searchHelper;

    /**
     * @var \Magento\Catalog\Model\Layer\Resolver
     */
    protected $layerResolver;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Search\Model\QueryFactory
     */
    private $queryFactory;

    /**
     * @var SummaryFactory
     */
    private $sumResourceFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Product constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param SummaryFactory $sumResourceFactory
     * @param HelperData $helperData
     * @param SearchHelper $searchHelper
     * @param LayerResolver $layerResolver
     * @param ObjectManager $objectManager
     * @param QueryFactory $queryFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        SummaryFactory $sumResourceFactory,
        HelperData $helperData,
        SearchHelper $searchHelper,
        LayerResolver $layerResolver,
        ObjectManager $objectManager,
        QueryFactory $queryFactory
    ) {
        $this->storeManager       = $storeManager;
        $this->sumResourceFactory = $sumResourceFactory;
        $this->helperData         = $helperData;
        $this->searchHelper       = $searchHelper;
        $this->layerResolver      = $layerResolver;
        $this->objectManager      = $objectManager;
        $this->queryFactory       = $queryFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseData()
    {
        $responseData['code'] = AutocompleteFields::PRODUCT;
        $responseData['data'] = [];

        if (!$this->canAddToResult()) {
            return $responseData;
        }

        $query                 = $this->queryFactory->get();
        $queryText             = $query->getQueryText();
        $productResultFields   = $this->helperData->getProductResultFieldsAsArray();
        $productResultFields[] = ProductFields::URL;

        $productCollection = $this->getProductCollection($queryText);

        foreach ($productCollection as $product) {
            $responseData['data'][] = array_intersect_key(
                $this->getProductData($product),
                array_flip($productResultFields)
            );
        }

        $responseData['size'] = $productCollection->getSize();
        $responseData['url']  = ($productCollection->getSize() > 0) ? $this->searchHelper->getResultUrl(
            $queryText
        ) : '';

        $query->saveNumResults($responseData['size']);
        $query->saveIncrementalPopularity();

        return $responseData;
    }

    /**
     * Retrive product collection by query text
     *
     * @param string $queryText
     * @return mixed
     */
    protected function getProductCollection($queryText)
    {
        $productResultNumber = $this->helperData->getProductResultNumber();

        $this->layerResolver->create(LayerResolver::CATALOG_LAYER_SEARCH);

        $productCollection = $this->layerResolver->get()
                                                 ->getProductCollection()
                                                 ->addAttributeToSelect(
                                                     [ProductFields::DESCRIPTION, ProductFields::SHORT_DESCRIPTION]
                                                 )
                                                 ->setPageSize($productResultNumber)
                                                 ->addAttributeToSort('relevance')
                                                 ->setOrder('relevance')
                                                 ->addSearchFilter($queryText);
        /** @var \Magento\Review\Model\ResourceModel\Review\Summary $sumResource */
        $sumResource = $this->sumResourceFactory->create();

        $sumResource->appendSummaryFieldsToCollection(
            $productCollection,
            $this->getStoreId(),
            \Magento\Review\Model\Review::ENTITY_PRODUCT_CODE
        );


        return $productCollection;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * Retrieve all product data
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    protected function getProductData($product)
    {
        /** @var \MageWorx\SearchSuiteAutocomplete\Block\Autocomplete\ProductAgregator $productAgregator */
        $productAgregator = $this->objectManager->create(
            'MageWorx\SearchSuiteAutocomplete\Block\Autocomplete\ProductAgregator'
        )
                                                ->setProduct($product);

        $data = [
            ProductFields::NAME              => $productAgregator->getName(),
            ProductFields::SKU               => $productAgregator->getSku(),
            ProductFields::IMAGE             => $productAgregator->getSmallImage(),
            ProductFields::REVIEWS_RATING    => $productAgregator->getReviewsRating(),
            ProductFields::SHORT_DESCRIPTION => $productAgregator->getShortDescription(),
            ProductFields::DESCRIPTION       => $productAgregator->getDescription(),
            ProductFields::PRICE             => $productAgregator->getPrice(),
            ProductFields::URL               => $productAgregator->getUrl()
        ];

        if ($product->getData('is_salable')) {
            $data[ProductFields::ADD_TO_CART] = $productAgregator->getAddToCartData();
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function canAddToResult()
    {
        return in_array(AutocompleteFields::PRODUCT, $this->helperData->getAutocompleteFieldsAsArray());
    }
}
