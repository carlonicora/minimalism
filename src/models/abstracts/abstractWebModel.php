<?php
namespace carlonicora\minimalism\models\abstracts;

use carlonicora\minimalism\exceptions\serviceNotFoundException;
use carlonicora\minimalism\services\factories\servicesFactory;
use carlonicora\minimalism\jsonapi\interfaces\responseInterface;
use carlonicora\minimalism\services\paths\factories\serviceFactory;
use carlonicora\minimalism\services\paths\paths;

abstract class abstractWebModel extends abstractModel {
    /** @var string */
    protected string $viewName='';

    /**
     * abstractWebModel constructor.
     * @param servicesFactory $services
     * @param array $passedParameters
     * @param array $file
     * @throws serviceNotFoundException
     */
    public function __construct(servicesFactory $services, array $passedParameters, array $file=null){
        parent::__construct($services, $passedParameters, $file);

        /** @var paths $paths */
        $paths = $this->services->service(serviceFactory::class);
        $this->response->addMeta('url', $paths->getUrl());
    }

    /**
     * @return responseInterface
     */
    public function generateData(): responseInterface{
        return $this->response;
    }

    /**
     * @return string
     */
    public function getViewName(): string {
        return $this->viewName;
    }
}