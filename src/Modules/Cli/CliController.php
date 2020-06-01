<?php
namespace CarloNicora\Minimalism\Modules\Cli;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers\AbstractCliController;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use CarloNicora\Minimalism\Core\Response;
use Exception;

class CliController extends AbstractCliController {
    /** @var ModelInterface|CliModel  */
    protected ModelInterface $model;

    /**
     * @return ResponseInterface
     * @throws Exception
     */
    public function render(): ResponseInterface {
        try {
            $this->model->preRender();

            $response = $this->model->run();
        } catch (Exception $e) {
            $response = new Response();
            $response->setData($e->getMessage());
        }

        $this->completeRender($response->getStatus(), $response->getData());

        $response->setNotHttpResponse();

        return $response;
    }
}