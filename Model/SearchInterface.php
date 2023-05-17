<?php

namespace MageWorx\SearchSuiteAutocomplete\Model;

/**
 * @api
 */
interface SearchInterface
{
    /**
     * Retrieve selected in config data
     *
     * @return array
     */
    public function getResponseData(): array;

    /**
     * Check if data used in search result
     *
     * @return bool
     */
    public function canAddToResult(): bool;
}
