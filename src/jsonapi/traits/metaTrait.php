<?php
namespace carlonicora\minimalism\jsonapi\traits;

trait metaTrait {
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

    /**
     * @param array $metas
     */
    public function addMetas(array $metas): void {
        if ($this->meta === null){
            $this->meta = [];
        }

        $this->meta = array_merge($this->meta, $metas);
    }
}