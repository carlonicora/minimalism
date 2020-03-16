<?php
namespace carlonicora\minimalism\jsonapi\resources;

trait hasMeta{
    /** @var array|null */
    public ?array $meta=null;

    /**
     * @param string $name
     * @param string $value
     */
    public function addMeta(string $name, string $value): void {
        if ($this->meta === null){
            $this->meta = [];
        }

        $this->meta[$name] = $value;
    }
}