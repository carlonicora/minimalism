<?php
namespace carlonicora\minimalism\core\controllers;

use carlonicora\minimalism\core\controllers\abstracts\abstractController;
use carlonicora\minimalism\core\services\exceptions\serviceNotFoundException;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\core\jsonapi\interfaces\responseInterface;
use carlonicora\minimalism\core\jsonapi\responses\dataResponse;
use carlonicora\minimalism\core\jsonapi\responses\errorResponse;
use carlonicora\minimalism\core\models\abstracts\abstractWebModel;
use carlonicora\minimalism\services\paths\factories\serviceFactory;
use carlonicora\minimalism\services\paths\paths;
use RuntimeException;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Exception;

class appController extends abstractController {
    /** @var Environment */
    private Environment $view;

    /**
     * apiController constructor.
     * @param servicesFactory $services
     * @param string|null $modelName
     * @param array|null $parameterValueList
     * @param array|null $parameterValues
     * @throws Exception
     */
    public function __construct(servicesFactory $services, string $modelName=null, array $parameterValueList=null, array $parameterValues=null){
        parent::__construct($services, $modelName, $parameterValueList, $parameterValues);

        $this->initialiseView();
    }

    /**
     *
     * @throws serviceNotFoundException
     */
    private function initialiseView(): void {
        /** @var paths $paths */
        $paths = $this->services->service(serviceFactory::class);

        /** @var abstractWebModel $model */
        $model = $this->model;
        if ($model->getViewName() !== '') {
            try {
                $twigLoader = new FilesystemLoader($paths->getRoot() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'views');
                $this->view = new Environment($twigLoader);
            } catch (Exception $e) {
                throw new RuntimeException('View failure: ' . $e->getMessage(), 404);
            }
        }
    }

    /**
     * @return string
     */
    public function render(): string{
        $error = $this->model->preRender();
        if ($error !== null){
            return $error->toJson();
        }

        $response = null;

        /** @var responseInterface $data */
        $data = $this->model->generateData();

        /**
        if (array_key_exists('forceRedirect', $data)) {
        header('Location:' . $data['forceRedirect']);
        exit;
        }
         */

        if ($this->model->getViewName() !== '') {
            try {
                $response = $this->view->render($this->model->getViewName() . '.twig', $data->toArray());
            } catch (Exception $e) {
                $data = new errorResponse(errorResponse::HTTP_STATUS_500, 'Failed to render the view');
            }
        }

        if ($response === null) {
            $response = $data->toJson();
        }

        $code = $data->getStatus();
        $GLOBALS['http_response_code'] = $code;
        header(dataResponse::generateProtocol() . ' ' . $code . ' ' . $data->generateText());

        $this->services->cleanNonPersistentVariables();
        $_SESSION['minimalismServices'] = $this->services;
        setcookie('minimalismServices', $this->services->serialiseCookies(), time() + (30 * 24 * 60 * 60));

        return $response;
    }
}