<?php
namespace CarloNicora\Minimalism\Core\Events\Abstracts;

use JsonException;

class AbstractErrorEvent extends AbstractEvent
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
            $message['error'] = $this->message;
        }

        if ($this->e !== null)
        {
            /**
             * @todo parse the trace output in a format which keeps the filepath
             *       relative to the project root. That way a unit test based on
             *       the output of this method can be written
             */
            $message['Exception'] = $this->e->getTrace();
        }

        return json_encode($message, JSON_THROW_ON_ERROR);
    }
}
