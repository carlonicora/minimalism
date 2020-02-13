<?php
namespace carlonicora\minimalism\businessObjects\abstracts;

use carlonicora\minimalism\abstracts\abstractConfigurations;
use carlonicora\minimalism\businessObjects\factories\businessObjectsFactory;
use carlonicora\minimalism\businessObjects\interfaces\businessObjectsArrayInterface;

abstract class abstractBusinessObjectsArray implements businessObjectsArrayInterface {

    /** @var abstractConfigurations */
    protected $configurations;
    /** @var string */
    protected $buinessObjectClass;
    /** @var abstractBusinessObject */
    protected $businessObject;

    /** @var string  */
    public const CHILDREN_WRAPPER = 'children';

    /**
     * abstractBusinessObjectsArray constructor.
     * @param businessObjectsFactory $businessObjectsFactory
     */
    public function __construct(businessObjectsFactory $businessObjectsFactory) {
        $this->businessObject = $businessObjectsFactory->create($this->buinessObjectClass);
    }

    /**
     * @inheritDoc
     */
    public function fromDbModelsArray(array $data): array {
        $result = [];

        foreach ($data as $row) {
            $formattedEntity = $this->businessObject->fromDbModel($row);
            $id = $formattedEntity[$this->businessObject->idField];
            $result[$id] = $formattedEntity;
        }

        if (empty($this->businessObject->parentId)) {
            return $result;
        }

        return $this->buildTree($result);
    }

    public function toDbModelsArray(array $data): array {
        $result = [];

        foreach ($data as $row) {
            $formattedEntity = $this->businessObject->toDbModel($row);
            $id = $formattedEntity[$this->businessObject->idField];
            $result[$id] = $formattedEntity;
        }

        return $result;
    }

    /**
     * @param array $elements
     * @param string $parentId
     * @return array
     */
    protected function buildTree(array &$elements, string $parentId = ''): array {
        $branch = [];

        foreach ($elements as $id => $element) {
            if ($element[$this->businessObject->parentId] === $parentId) {
                $children = $this->buildTree($elements, $id);

                if ($children) {
                    $element[self::CHILDREN_WRAPPER] = $children;
                }

                $branch[$id] = $element;
                unset($elements[$id]);
            }
        }

        return $branch;
    }

}