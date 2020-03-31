<?php
namespace carlonicora\minimalism\configurations;

use carlonicora\minimalism\services\mailer\configurations\mailerConfigurations;

class configData {
    /** @var mailerConfigurations  */
    private mailerConfigurations $mailingConfigurations;

    public function __construct() {
        $this->mailingConfigurations = new mailerConfigurations();
    }

    /**
     * @return mailerConfigurations
     */
    public function mailer() : mailerConfigurations {
        return $this->mailingConfigurations;
    }
}