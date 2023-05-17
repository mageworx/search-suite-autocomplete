<?php

namespace MageWorx\SearchSuiteAutocomplete\Model\Search;

use Magento\Search\Helper\Data as SearchHelper;
use Magento\Search\Model\AutocompleteInterface;
use MageWorx\SearchSuiteAutocomplete\Helper\Data as HelperData;
use MageWorx\SearchSuiteAutocomplete\Model\SearchInterface;
use MageWorx\SearchSuiteAutocomplete\Model\Source\AutocompleteFields;

/**
 * Suggested model. Return suggested data used in search autocomplete
 */
class Suggested implements SearchInterface
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
     * @var AutocompleteInterface;
     */
    protected AutocompleteInterface $autocomplete;

    /**
     * Suggested constructor.
     *
     * @param HelperData $helperData
     * @param SearchHelper $searchHelper
     * @param AutocompleteInterface $autocomplete
     */
    public function __construct(
        HelperData            $helperData,
        SearchHelper          $searchHelper,
        AutocompleteInterface $autocomplete
    ) {
        $this->helperData   = $helperData;
        $this->searchHelper = $searchHelper;
        $this->autocomplete = $autocomplete;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseData(): array
    {
        $responseData['code'] = AutocompleteFields::SUGGEST;
        $responseData['data'] = [];

        if (!$this->canAddToResult()) {
            return $responseData;
        }

        $suggestResultNumber = $this->helperData->getSuggestedResultNumber();

        $autocompleteData = $this->autocomplete->getItems();
        $autocompleteData = array_slice($autocompleteData, 0, $suggestResultNumber);
        foreach ($autocompleteData as $item) {
            $item                   = $item->toArray();
            $item['url']            = $this->searchHelper->getResultUrl($item['title']);
            $responseData['data'][] = $item;
        }

        return $responseData;
    }

    /**
     * {@inheritdoc}
     */
    public function canAddToResult(): bool
    {
        return in_array(AutocompleteFields::SUGGEST, $this->helperData->getAutocompleteFieldsAsArray());
    }
}
