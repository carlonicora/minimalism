<?php
namespace carlonicora\minimalism\dataObjects;

class parameter {
    /** @var string */
    public $name;

    /** @var string */
    public $type;

    /** @var bool */
    public $isRequired;

    /** @var int */
    public $order;

    /** @var mixed */
    public $value;

    /**
     * parameter constructor.
     * @param string $name
     * @param int $order
     * @param bool $isRequired
     * @param string $type
     */
    public function __construct(string $name, int $order, bool $isRequired=false, string $type='string') {
        $this->name = $name;
        $this->isRequired = $isRequired;
        $this->order = $order;
        $this->type = $type;
    }
}