<?php

namespace MageWorx\SearchSuiteAutocomplete\Api;

interface SearchInterface
{
    /**
     * Return
     *
     * @api
     * @param string $search
     * @return string
     */
    public function getSearchResult($search);
}
