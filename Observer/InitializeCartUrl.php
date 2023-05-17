<?php

namespace MageWorx\SearchSuiteAutocomplete\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;

/**
 * AddToCart class observe add_to_cart action for modify return url
 */
class InitializeCartUrl implements ObserverInterface
{
    /**
     * @var UrlInterface
     */
    protected UrlInterface $url;

    /**
     * AddToCart constructor.
     *
     * @param UrlInterface $url
     */
    public function __construct(
        UrlInterface $url
    ) {
        $this->url = $url;
    }

    /**
     * This method set return url to 'checkout/cart'
     * after AddToCart action from autocomplete form
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $request                   = $observer->getEvent()->getRequest();
        $isSearchSuiteAutocomplete = $request->getParam('mageworx_searchsuiteautocomplete', false);

        if ($isSearchSuiteAutocomplete) {
            $request->setParam('return_url', $this->url->getUrl('checkout/cart'));
        }
    }
}
