<?php

namespace MageWorx\SearchSuiteAutocomplete\Block\Autocomplete;

use Magento\Catalog\Block\Product\ReviewRendererInterface;
use Magento\Catalog\Helper\ImageFactory;
use Magento\Catalog\Helper\Output as CatalogHelperOutput;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\DataObject;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\Url\Helper\Data as UrlHelper;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use MageWorx\SearchSuiteAutocomplete\Block\Product as ProductBlock;


/**
 * ProductAgregator class for autocomplete data
 *
 * @method Product setProduct(Product $product)
 */
class ProductAgregator extends DataObject
{
    /**
     * @var ProductBlock
     */
    protected ProductBlock $productBlock;

    /**
     * @var UrlHelper
     */
    protected UrlHelper $urlHelper;

    /**
     * @var FormKey
     */
    protected FormKey $formKey;

    /**
     * @var AssetRepository
     */
    protected AssetRepository $assetRepo;

    /**
     * @var CatalogHelperOutput
     */
    protected CatalogHelperOutput $catalogHelperOutput;

    /**
     * @var Escaper
     */
    protected Escaper $escaper;

    /**
     * @var StringUtils
     */
    protected StringUtils $string;

    /**
     * @var ImageFactory
     */
    private ImageFactory $imageFactory;

    /**
     * ProductAgregator constructor.
     *
     * @param ImageFactory $imageFactory
     * @param ProductBlock $productBlock
     * @param StringUtils $string
     * @param UrlHelper $urlHelper
     * @param Repository $assetRepo
     * @param CatalogHelperOutput $catalogHelperOutput
     * @param FormKey $formKey
     * @param Escaper $escaper
     */
    public function __construct(
        ImageFactory        $imageFactory,
        ProductBlock        $productBlock,
        StringUtils         $string,
        UrlHelper           $urlHelper,
        Repository          $assetRepo,
        CatalogHelperOutput $catalogHelperOutput,
        FormKey             $formKey,
        Escaper             $escaper
    ) {
        $this->imageFactory        = $imageFactory;
        $this->productBlock        = $productBlock;
        $this->string              = $string;
        $this->urlHelper           = $urlHelper;
        $this->assetRepo           = $assetRepo;
        $this->catalogHelperOutput = $catalogHelperOutput;
        $this->formKey             = $formKey;
        $this->escaper             = $escaper;
    }

    /**
     * Retrieve product name
     *
     * @return string
     */
    public function getName(): string
    {
        return strip_tags(html_entity_decode((string)$this->getProduct()->getName()));
    }

    /**
     * Retrieve product sku
     *
     * @return string
     */
    public function getSku(): string
    {
        return $this->getProduct()->getSku();
    }

    /**
     * Retrieve product small image url
     *
     * @return string
     */
    public function getSmallImage(): string
    {
        $product = $this->getProduct();

        $image = $this->imageFactory->create()->init($product, 'product_small_image');

        return $image->getUrl();
    }

    /**
     * Retrieve product url
     *
     * @param string|null $route
     * @param array|null $params
     * @return string
     */
    public function getUrl(?string $route = '', ?array $params = []): string
    {
        return $this->productBlock->getProductUrl($this->getProduct());
    }

    /**
     * Retrieve product reviews rating html
     *
     * @return string
     */
    public function getReviewsRating(): string
    {
        return $this->productBlock->getReviewsSummaryHtml(
            $this->getProduct(),
            ReviewRendererInterface::SHORT_VIEW,
            true
        );
    }

    /**
     * Retrieve product short description
     *
     * @return string
     */
    public function getShortDescription(): string
    {
        $shortDescription = html_entity_decode((string)$this->getProduct()->getShortDescription());

        return $this->cropDescription($shortDescription);
    }

    /**
     * Crop description to 50 symbols
     *
     * @param string $html
     * @return string
     */
    protected function cropDescription(string $html): string
    {
        $string = strip_tags($html);

        return (strlen($string) > 50) ? $this->string->substr($string, 0, 50) . '...' : $string;
    }

    /**
     * Retrieve product description
     *
     * @return string
     */
    public function getDescription(): string
    {
        $description = html_entity_decode((string)$this->getProduct()->getDescription());

        return $this->cropDescription($description);
    }

    /**
     * Retrieve product price
     *
     * @return string
     */
    public function getPrice(): string
    {
        return $this->productBlock->getProductPrice(
            $this->getProduct()
        );
    }

    /**
     * Retrieve product add to cart data
     *
     * @return array
     * @throws LocalizedException
     */
    public function getAddToCartData(): array
    {
        $formUrl             = $this->productBlock->getAddToCartUrl(
            $this->getProduct(),
            ['mageworx_searchsuiteautocomplete' => true]
        );
        $productId           = $this->getProduct()->getEntityId();
        $paramNameUrlEncoded = ActionInterface::PARAM_NAME_URL_ENCODED;
        $urlEncoded          = $this->urlHelper->getEncodedUrl($formUrl);
        $formKey             = $this->formKey->getFormKey();

        return [
            'formUrl'             => $formUrl,
            'productId'           => $productId,
            'paramNameUrlEncoded' => $paramNameUrlEncoded,
            'urlEncoded'          => $urlEncoded,
            'formKey'             => $formKey
        ];
    }
}
