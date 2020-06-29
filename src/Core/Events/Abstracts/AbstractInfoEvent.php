<?php
namespace CarloNicora\Minimalism\Core\Events\Abstracts;

use JsonException;

class AbstractInfoEvent extends AbstractEvent
{
    /**
     * @return string
     * @throws JsonException
     * @noinspection PhpDocRedundantThrowsInspection
     */
    public function generateMessage(): string
    {
        $message = [
            'time' => date('Y-m-d H:i:s', $this->getTime()),
            'service' => $this->serviceName,
            'id' => $this->id
        ];

        if ($this->message !== null) {
            $message['details'] = $this->message;
        }

        return json_encode($message, JSON_THROW_ON_ERROR);
    }
}