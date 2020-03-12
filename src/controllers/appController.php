<?php
namespace carlonicora\minimalism\controllers;

use carlonicora\minimalism\abstracts\abstractController;
use carlonicora\minimalism\abstracts\abstractWebModel;
use carlonicora\minimalism\dataObjects\errorObject;
use carlonicora\minimalism\dataObjects\responseObject;
use carlonicora\minimalism\helpers\errorReporter;
use carlonicora\minimalism\helpers\sessionManager;
use carlonicora\minimalism\interfaces\responseInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Exception;

class appController extends abstractController {
    /** @var Environment */
    private Environment $view;

    /**
     * apiController constructor.
     * @param $configurations
     * @param null $modelName
     * @param null $parameterValueList
     * @param null $parameterValues
     */
    public function __construct($configurations, $modelName = null, $parameterValueList = null, $parameterValues = null) {
        parent::__construct($configurations, $modelName, $parameterValueList, $parameterValues);

        $this->initialiseView();
    }

    /**
     *
     */
    private function initialiseView(): void {
        /** @var abstractWebModel $model */
        $model = $this->model;
        if ($model->getViewName() !== '') {
            try {
                $twigLoader = new FilesystemLoader($this->configurations->appDirectory . DIRECTORY_SEPARATOR . 'views');
                $this->view = new Environment($twigLoader);
            } catch (Exception $exception) {
                errorReporter::report($this->configurations, 4, null, 404);
            }
        }
    }

    /**
     * @return string
     */
    public function render(): string{
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
                $data = new errorObject(errorObject::HTTP_STATUS_500, 'Failed to render the view');
            }
        }

        if ($response === null) {
            $response = $data->toJson();
        }

        $code = $data->getStatus();
        $GLOBALS['http_response_code'] = $code;
        header(responseObject::generateProtocol() . ' ' . $code . ' ' . $data->generateText());

        $sessionManager = new sessionManager();
        $sessionManager->saveSession($this->configurations);

        return $response;
    }
}