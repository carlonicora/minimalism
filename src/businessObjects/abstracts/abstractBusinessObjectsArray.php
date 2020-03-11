<?php
namespace carlonicora\minimalism\businessObjects\abstracts;

use carlonicora\minimalism\abstracts\abstractConfigurations;
use carlonicora\minimalism\businessObjects\interfaces\businessObjectsArrayInterface;

abstract class abstractBusinessObjectsArray implements businessObjectsArrayInterface {

    /** @var abstractConfigurations */
    protected abstractConfigurations $configurations;
    /** @var string */
    protected string $buinessObjectClass;
    /** @var abstractBusinessObject */
    protected abstractBusinessObject $businessObject;

    /** @var string  */
    public const CHILDREN_WRAPPER = 'children';

    /**
     * abstractBusinessObjectsArray constructor.
     * @param abstractBusinessObject $businessObject
     */
    public function __construct(abstractBusinessObject $businessObject) {
        $this->businessObject = $businessObject;
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
    protected function buildTree(array &$elements, string $parentId = null): array {
        $branch = [];

        foreach ($elements as $id => $element) {
            $currentParentId = $element[$this->businessObject->parentId] ?? null;
            if ($parentId === null || $currentParentId === $parentId) {
                $children = $this->buildTree($elements, $id);

                if ($children) {
                    $element[self::CHILDREN_WRAPPER] = $children;
                }

                $branch[$id] = $element;
                unset($elements[$id]);
            }
        }

        return array_values($branch);
    }

}