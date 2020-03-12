<?php
namespace carlonicora\minimalism\controllers;

use carlonicora\minimalism\abstracts\abstractController;
use carlonicora\minimalism\abstracts\abstractWebModel;
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
        $data = [];

        if (array_key_exists('forceRedirect', $data)) {
            header('Location:' . $data['forceRedirect']);
            exit;
        }

        /** @var responseInterface $response */
        $response = $this->model->generateData();

        if ($this->model->getViewName() !== '') {
            try {
                $response = $this->view->render($this->model->getViewName() . '.twig', $response->toArray());
            } catch (Exception $e) {
                errorReporter::report($this->configurations, '', 'Failed to render the view', 500);
                exit;
            }
        } else {
            $response = json_encode($data, JSON_THROW_ON_ERROR, 512);
        }

        $sessionManager = new sessionManager();
        $sessionManager->saveSession($this->configurations);

        return $response;
    }
}