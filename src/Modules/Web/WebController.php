<?php
namespace CarloNicora\Minimalism\Modules\Web;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers\AbstractWebController;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ApiModelInterface;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ControllerInterface;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use CarloNicora\Minimalism\Core\JsonApi\Events\JsonApiInfoEvents;
use CarloNicora\Minimalism\Core\JsonApi\JsonApiResponse;
use RuntimeException;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Exception;

class WebController extends AbstractWebController {
    /** @var Environment */
    private Environment $view;

    /** @var ModelInterface|ApiModelInterface|WebModel */
    protected ModelInterface $model;

    /**
     * @throws Exception
     */
    protected function initialiseView(): void {
        /** @var WebModel $model */
        $model = $this->model;
        if ($model->getViewName() !== '') {
            try {
                $paths = [];
                $defaultDirectory = $this->services->paths()->getRoot() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Views';
                if (file_exists($defaultDirectory)){
                    $paths[] = $defaultDirectory;
                }

                foreach ($this->services->paths()->getServicesViewsDirectories() as $additionalPaths) {
                    $paths[] = $additionalPaths;
                }

                $twigLoader = new FilesystemLoader($paths);
                $this->view = new Environment($twigLoader);
            } catch (Exception $e) {
                throw new RuntimeException('View failure: ' . $e->getMessage(), 404);
            }
        }
        $this->services->logger()->info()->log(JsonApiInfoEvents::TWIG_ENGINE_INITIALISED());
    }

    /**
     * @return ControllerInterface
     */
    public function postInitialise(): ControllerInterface
    {
        return $this;
    }

    /**
     * @return ResponseInterface
     * @throws Exception
     */
    public function render() : ResponseInterface
    {
        try {
            $this->preRender();

            $this->services->logger()->info()->log(JsonApiInfoEvents::PRE_RENDER_COMPLETED());

            /** @var JsonApiResponse $response */
            $response = $this->model->generateData();

            $this->services->logger()->info()->log(JsonApiInfoEvents::DATA_GENERATED());

            if ($this->model->getViewName() !== '') {
                try {
                    foreach ($this->model->getTwigExtensions() ?? [] as $twigExtension){
                        $this->view->addExtension($twigExtension);
                    }
                    $response->setData($this->view->render($this->model->getViewName() . '.twig', $response->getDocument()->prepare()));


                    $this->services->logger()->info()->log(JsonApiInfoEvents::DATA_MERGED($this->model->getViewName()));
                } catch (Exception $e) {
                    $response = $this->model->generateResponseFromError(new Exception('Failed to render the view', ResponseInterface::HTTP_STATUS_500));
                }
            }
        } catch (Exception $e) {
            $response = $this->model->generateResponseFromError($e);
        }

        $response->setContentType('text/html');

        /**
         * @todo ResponseInterface getStatus is of string type
         */
        $this->completeRender((int)$response->getStatus(), $response->getData());

        $this->services->logger()->info()->log(JsonApiInfoEvents::RENDER_COMPLETE());

        return $response;
    }
}
