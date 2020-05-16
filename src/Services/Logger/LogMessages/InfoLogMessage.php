<?php
namespace CarloNicora\Minimalism\Services\Logger\LogMessages;

use CarloNicora\Minimalism\Services\Logger\Abstracts\AbstractLogMessage;
use JsonException;

class InfoLogMessage extends AbstractLogMessage
{
    /**
     * @return string
     * @throws JsonException
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