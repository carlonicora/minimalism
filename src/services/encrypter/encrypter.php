<?php /** @noinspection UnusedConstructorDependenciesInspection */

namespace carlonicora\minimalism\services\encrypter;

use carlonicora\minimalism\core\services\abstracts\abstractService;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\core\services\interfaces\serviceConfigurationsInterface;
use carlonicora\minimalism\services\encrypter\configurations\encrypterConfigurations;
use Hashids\Hashids;

class encrypter extends abstractService {
    /** @var encrypterConfigurations  */
    private encrypterConfigurations $configData;

    /** @var Hashids */
    private Hashids $hashids;

    /**
     * encrypter constructor.
     * @param serviceConfigurationsInterface $configData
     * @param servicesFactory $services
     */
    public function __construct(serviceConfigurationsInterface $configData, servicesFactory $services) {
        parent::__construct($configData, $services);

        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->configData = $configData;

        $this->hashids = new Hashids($this->configData->key, $this->configData->length);
    }

    /**
     * @param int $id
     * @return string
     */
    public function encryptId(int $id): string {
        return $this->hashids->encodeHex($id);
    }

    /**
     * @param string $encryptedId
     * @return int
     */
    public function decryptId(string $encryptedId): int {
        return (int)$this->hashids->decodeHex($encryptedId);
    }
}