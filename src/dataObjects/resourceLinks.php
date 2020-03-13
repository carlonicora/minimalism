<?php
namespace carlonicora\minimalism\dataObjects;

trait resourceLinks {
    /** @var array|null  */
    protected ?array $links=null;

    /**
     * @return bool
     */
    protected function hasLinks() : bool {
        return $this->links !== null;
    }

    /**
     * @param resourceLink $link
     */
    public function addLink(resourceLink $link): void {
        if ($this->links === null){
            $this->links = [];
        }

        $this->links[] = $link;
    }

    /**
     * @return array
     */
    protected function linksToArray(): array {
        $response = [];

        /** @var resourceLink $link */
        foreach ($this->links ?? [] as $link){
            $response[] = $link->toArray();
        }

        return $response;
    }
}