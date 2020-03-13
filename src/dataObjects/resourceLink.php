<?php
namespace carlonicora\minimalism\dataObjects;

class resourceLink {
    use resourceMeta;

    /** @var string  */
    private string $href;

    /** @var string  */
    private string $name;

    /**
     * resourceLink constructor.
     * @param string $name
     * @param string $url
     */
    public function __construct(string $name, string $url){
        $this->href = $url;
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function toArray() : array {
        $response = [];

        if ($this->meta === null){
            $response[$this->name] = $this->href;
        } else {
            $response[$this->name] = [
                'href' => $this->href,
                'meta' => $this->meta
            ];
        }

        return $response;
    }
}