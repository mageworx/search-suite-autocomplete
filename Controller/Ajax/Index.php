<?php

namespace MageWorx\SearchSuiteAutocomplete\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Search\Model\QueryFactory;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\SearchSuiteAutocomplete\Helper\Data as HelperData;
use MageWorx\SearchSuiteAutocomplete\Model\Search as SearchModel;

/**
 * SearchSuiteAutocomplete ajax controller
 */
class Index extends Action
{
    /**
     * @var HelperData
     */
    protected HelperData $helperData;
    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;
    /**
     * @var QueryFactory
     */
    private QueryFactory $queryFactory;
    /**
     * @var SearchModel
     */
    private SearchModel $searchModel;

    /**
     * Index constructor.
     *
     * @param HelperData $helperData
     * @param Context $context
     * @param QueryFactory $queryFactory
     * @param StoreManagerInterface $storeManager
     * @param SearchModel $searchModel
     */
    public function __construct(
        HelperData            $helperData,
        Context               $context,
        QueryFactory          $queryFactory,
        StoreManagerInterface $storeManager,
        SearchModel           $searchModel
    ) {
        $this->helperData   = $helperData;
        $this->storeManager = $storeManager;
        $this->queryFactory = $queryFactory;
        $this->searchModel  = $searchModel;
        parent::__construct($context);
    }

    /**
     * Retrieve json of result data
     *
     * @return ResultInterface
     * @throws NoSuchEntityException
     */
    public function execute(): ResultInterface
    {
        $query = $this->queryFactory->get();
        $query->setStoreId($this->storeManager->getStore()->getId());

        $responseData = [];
        if ($query->getQueryText() != '') {
            $query->setId(0)->setIsActive(1)->setIsProcessed(1);
            $responseData['result'] = $this->searchModel->getData();
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($responseData);

        return $resultJson;
    }
}
