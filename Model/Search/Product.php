<?php

namespace MageWorx\SearchSuiteAutocomplete\Model\Search;

use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface as ObjectManager;
use Magento\Review\Model\ResourceModel\Review\SummaryFactory;
use Magento\Review\Model\Review;
use Magento\Search\Helper\Data as SearchHelper;
use Magento\Search\Model\QueryFactory;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\SearchSuiteAutocomplete\Block\Autocomplete\ProductAgregator;
use MageWorx\SearchSuiteAutocomplete\Helper\Data as HelperData;
use MageWorx\SearchSuiteAutocomplete\Model\SearchInterface;
use MageWorx\SearchSuiteAutocomplete\Model\Source\AutocompleteFields;
use MageWorx\SearchSuiteAutocomplete\Model\Source\ProductFields;

/**
 * Product model. Return product data used in search autocomplete
 */
class Product implements SearchInterface
{
    /**
     * @var HelperData
     */
    protected HelperData $helperData;

    /**
     * @var SearchHelper
     */
    protected SearchHelper $searchHelper;

    /**
     * @var LayerResolver
     */
    protected LayerResolver $layerResolver;

    /**
     * @var ObjectManager
     */
    protected ObjectManager $objectManager;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var QueryFactory
     */
    private QueryFactory $queryFactory;

    /**
     * @var SummaryFactory
     */
    private SummaryFactory $sumResourceFactory;

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
        SummaryFactory        $sumResourceFactory,
        HelperData            $helperData,
        SearchHelper          $searchHelper,
        LayerResolver         $layerResolver,
        ObjectManager         $objectManager,
        QueryFactory          $queryFactory
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
     * @throws LocalizedException
     */
    public function getResponseData(): array
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
     * {@inheritdoc}
     */
    public function canAddToResult(): bool
    {
        return in_array(AutocompleteFields::PRODUCT, $this->helperData->getAutocompleteFieldsAsArray());
    }

    /**
     * Retrive product collection by query text
     *
     * @param string $queryText
     * @return ProductCollection
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function getProductCollection(string $queryText): ProductCollection
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
                                                 ->setOrder('relevance');

        /**
         * fixes a bug when re-adding a Search Filter
         *
         * @see \Magento\CatalogSearch\Model\Layer\Search\Plugin\CollectionFilter
         */
        if ($this->queryFactory->get()->isQueryTextShort()) {
            $productCollection->addSearchFilter($queryText);
        }

        $sumResource = $this->sumResourceFactory->create();

        $sumResource->appendSummaryFieldsToCollection(
            $productCollection,
            $this->getStoreId(),
            Review::ENTITY_PRODUCT_CODE
        );

        return $productCollection;
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    public function getStoreId(): int
    {
        return (int)$this->storeManager->getStore()->getId();
    }

    /**
     * Retrieve all product data
     *
     * @param ProductModel $product
     * @return array
     * @throws LocalizedException
     */
    protected function getProductData(ProductModel $product): array
    {
        /** @var ProductAgregator $productAgregator */
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
}
