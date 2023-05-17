<?php


namespace MageWorx\SearchSuiteAutocomplete\Model;

use Magento\Framework\ObjectManagerInterface as ObjectManager;
use UnexpectedValueException;

/**
 * SearchFactory class for Search model
 */
class SearchFactory
{
    /**
     * @var ObjectManager|null
     */
    protected ?ObjectManager $objectManager = null;

    /**
     * @var array
     */
    protected array $map;

    /**
     * Factory constructor
     *
     * @param ObjectManager $objectManager
     * @param array $map
     */
    public function __construct(
        ObjectManager $objectManager,
        array         $map = []
    ) {
        $this->objectManager = $objectManager;
        $this->map           = $map;
    }

    /**
     *
     * @param string $param
     * @param array $arguments
     * @return SearchInterface
     * @throws UnexpectedValueException
     */
    public function create(string $param, array $arguments = []): SearchInterface
    {
        if (isset($this->map[$param])) {
            $instance = $this->objectManager->create($this->map[$param], $arguments);
        } else {
            $instance = $this->objectManager->create(
                '\MageWorx\SearchSuiteAutocomplete\Model\Search\Suggested',
                $arguments
            );
        }

        if (!$instance instanceof SearchInterface) {
            throw new UnexpectedValueException(
                'Class ' . get_class(
                    $instance
                ) . ' should be an instance of \MageWorx\SearchSuiteAutocomplete\Model\SearchInterface'
            );
        }

        return $instance;
    }
}
