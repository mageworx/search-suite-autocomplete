<?php

namespace MageWorx\SearchSuiteAutocomplete\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * AddToCart class observe add_to_cart action for modify return url
 */
class InitializeCartUrl implements ObserverInterface
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * AddToCart constructor.
     *
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Magento\Framework\UrlInterface $url
    ) {
        $this->url = $url;
    }

    /**
     * This method set return url to 'checkout/cart'
     * after AddToCart action from autocomplete form
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request                   = $observer->getEvent()->getRequest();
        $isSearchSuiteAutocomplete = $request->getParam('mageworx_searchsuiteautocomplete', false);

        if ($isSearchSuiteAutocomplete) {
            $request->setParam('return_url', $this->url->getUrl('checkout/cart'));
        }
    }
}
