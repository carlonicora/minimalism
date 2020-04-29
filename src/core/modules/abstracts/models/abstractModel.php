<?php
namespace carlonicora\minimalism\core\modules\abstracts\models;

use carlonicora\minimalism\core\services\exceptions\serviceNotFoundException;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\services\logger\logger;
use Exception;
use RuntimeException;

abstract class abstractModel {
    /** @var servicesFactory */
    protected servicesFactory $services;

    /** @var array */
    protected ?array $file;

    /** @var string */
    public ?string $redirectPage;

    /** @var array */
    protected array $parameters=[];

    /** @var logger  */
    protected logger $logger;

    /**
     * model constructor.
     * @param servicesFactory $services
     * @param array $passedParameters
     * @param array $file
     * @throws serviceNotFoundException|Exception
     */
    public function __construct(servicesFactory $services, array $passedParameters, array $file=null){
        $this->services = $services;

        $this->logger = $services->service(logger::class);

        $this->file = $file;

        $this->buildParameters($passedParameters);

        $this->logger->addSystemEvent(null, 'Model parameters built');

        $this->redirectPage = null;
    }

    /**
     * @param array $passedParameters
     * @throws Exception
     */
    private function buildParameters(array $passedParameters): void{
        if ($this->parameters !== null) {
            $parameters = $this->getParameters();

            foreach ($parameters ?? [] as $parameterKey=>$value) {
                $parameterName = $value;
                $isParameterRequired = false;
                $isParameterEncrypted = false;

                if (is_array($value)) {
                    $parameterName = $value['name'];
                    $isParameterRequired = $value['required'] ?? false;
                    $isParameterEncrypted = $value['encrypted'] ?? false;
                }

                if (array_key_exists($parameterKey, $passedParameters)) {
                    if ($passedParameters[$parameterKey] !== null
                        && ($isParameterEncrypted || (!empty($this->encryptedParameters) && in_array($parameterName, $this->encryptedParameters, true)))
                    ) {
                        $this->$parameterName = $this->decryptParameter($passedParameters[$parameterKey]);
                    } else {
                        $this->$parameterName = $passedParameters[$parameterKey];
                    }
                } elseif ($isParameterRequired){
                    throw new RuntimeException('Required parameter ' . $parameterName . ' missing.', 412);
                }
            }
        }
    }

    /**
     * @param string $parameter
     * @return string
     */
    protected function decryptParameter(string $parameter) : string {
        return $parameter;
    }

    /**
     * @return array
     */
    protected function getParameters(): array {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function redirect(): string {
        return $this->redirectPage ?? '';
    }

    /**
     *
     */
    abstract public function preRender();

    /**
     * @param int|null $code
     * @param string|null $response
     * @return mixed
     */
    public function postRender(?int $code, ?string $response): void{}
}