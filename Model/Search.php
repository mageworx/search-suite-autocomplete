<?php

namespace MageWorx\SearchSuiteAutocomplete\Model;

use MageWorx\SearchSuiteAutocomplete\Helper\Data as HelperData;

/**
 * Search class returns needed search data
 */
class Search
{
    /**
     * @var HelperData
     */
    protected HelperData $helperData;

    /**
     * @var SearchFactory
     */
    protected SearchFactory $searchFactory;

    /**
     * Search constructor.
     *
     * @param HelperData $helperData
     * @param SearchFactory $searchFactory
     */
    public function __construct(
        HelperData    $helperData,
        SearchFactory $searchFactory
    ) {
        $this->helperData    = $helperData;
        $this->searchFactory = $searchFactory;
    }

    /**
     * Retrieve suggested, product data
     *
     * @return array
     */
    public function getData(): array
    {
        $data               = [];
        $autocompleteFields = $this->helperData->getAutocompleteFieldsAsArray();

        foreach ($autocompleteFields as $field) {
            $data[] = $this->searchFactory->create($field)->getResponseData();
        }

        return $data;
    }
}
