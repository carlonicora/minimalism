<?php
namespace carlonicora\minimalism\services\security\configurations;

use carlonicora\minimalism\databases\security\tables\auth;
use carlonicora\minimalism\databases\security\tables\clients;
use carlonicora\minimalism\exceptions\dbConnectionException;
use carlonicora\minimalism\exceptions\serviceNotFoundException;
use carlonicora\minimalism\services\database\database;
use carlonicora\minimalism\services\database\factories\serviceFactory;
use carlonicora\minimalism\services\factories\servicesFactory;
use carlonicora\minimalism\services\security\interfaces\securityClientInterface;
use carlonicora\minimalism\services\security\interfaces\securitySessionInterface;

class securityConfigurations {
    /** @var string  */
    public string $httpHeaderSignature;

    /** @var string|null  */
    public ?string $clientId=null;

    /** @var string|null  */
    public ?string $clientSecret=null;

    /** @var string|null  */
    public ?string $publicKey=null;

    /** @var string|null  */
    public ?string $privateKey=null;

    /** @var securityClientInterface|null  */
    public ?securityClientInterface $securityClient=null;

    /** @var securitySessionInterface|null  */
    public ?securitySessionInterface $securitySession=null;

    /**
     * securityConfigurations constructor.
     */
    public function __construct() {
        $this->httpHeaderSignature = getenv('HEADER_SIGNATURE') ?: 'Minimalism-Signature';
    }

    /**
     * @param servicesFactory $services
     * @throws serviceNotFoundException
     */
    public function setupSecurityInterfaces(servicesFactory $services): void {
        /** @var database $database */
        $database = $services->service(serviceFactory::class);

        try {
            /** @var securityClientInterface $securityClient */
            $securityClient = $database->create(clients::class);
            $this->securityClient = $securityClient;

            /** @var securitySessionInterface $securitySession */
            $securitySession = $database->create(auth::class);
            $this->securitySession = $securitySession;
        } catch (dbConnectionException $e) {}
    }
}