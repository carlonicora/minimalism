<?php
namespace carlonicora\minimalism\jsonapi\resources;

trait hasLinks {
    /** @var array|null  */
    protected ?array $links=null;

    /**
     * @return bool
     */
    protected function hasLinks() : bool {
        return $this->links !== null;
    }

    /**
     * @param string $key
     * @param string $url
     * @param array|null $meta
     */
    public function addLink(string $key, string $url, array $meta = null): void {
        if ($this->links === null){
            $this->links = [];
        }

        if (empty($meta)) {
            $this->links[$key] = $url;
        } else {
            $this->links[$key] = [
                'href' => $url,
                'meta' => $meta
            ];
        }
    }
}