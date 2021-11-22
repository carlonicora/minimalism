<?php
namespace CarloNicora\Minimalism\Factories;

use Exception;

class MinimalismFactories
{
    /** @var ServiceFactory  */
    private ServiceFactory $serviceFactory;

    /** @var ModelFactory  */
    private ModelFactory $modelFactory;

    /** @var ObjectFactory  */
    private ObjectFactory $objectFactory;

    /**
     * @throws Exception
     */
    public function __construct(
    )
    {
        $this->serviceFactory = new ServiceFactory();
        $this->modelFactory = new ModelFactory(
            serviceFactory: $this->serviceFactory,
        );
        $this->objectFactory = new ObjectFactory(
            serviceFactory: $this->serviceFactory,
        );
    }

    /**
     * @return ModelFactory
     */
    public function getModelFactory(): ModelFactory
    {
        return $this->modelFactory;
    }

    /**
     * @return ObjectFactory
     */
    public function getObjectFactory(): ObjectFactory
    {
        return $this->objectFactory;
    }

    /**
     * @return ServiceFactory
     */
    public function getServiceFactory(): ServiceFactory
    {
        return $this->serviceFactory;
    }

    /**
     * @param string $fileName
     * @return string
     */
    public static function getNamespace(
        string $fileName
    ): string
    {
        $serviceName = pathinfo($fileName, flags: PATHINFO_FILENAME);
        $code = file_get_contents($fileName);

        $pattern = '#^namespace\s+(.+?);$#sm';
        if (!preg_match($pattern, $code, matches: $m)) {
            /** @noinspection SyntaxError */
            $normalisedCode = preg_replace(pattern: '#(*BSR_ANYCRLF)\R#', replacement: "\n", subject: $code);
            preg_match($pattern, $normalisedCode, matches: $m);
        }

        $namespace = $m[1] ?? '';

        return $namespace . '\\' . $serviceName;
    }
}