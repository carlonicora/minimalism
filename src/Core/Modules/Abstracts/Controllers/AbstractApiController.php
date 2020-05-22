<?php
namespace CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers\Traits\CompleteRenderTrait;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ApiModelInterface;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ControllerInterface;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use Exception;

abstract class AbstractApiController extends AbstractController
{
    use CompleteRenderTrait;

    /** @var string */
    public string $verb;

    /** @var ModelInterface|ApiModelInterface  */
    protected ModelInterface $model;

    /**
     * abstractController constructor.
     * @param ServicesFactory $services
     * @throws Exception
     */
    public function __construct(ServicesFactory $services)
    {
        parent::__construct($services);

        $this->initialiseVerb();
    }

    /**
     *
     */
    protected function initialiseVerb(): void
    {
        $this->verb = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($this->verb === 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] === 'DELETE') {
                $this->verb = 'DELETE';
            } elseif ($_SERVER['HTTP_X_HTTP_METHOD'] === 'PUT') {
                $this->verb = 'PUT';
            }
        }
    }

    /**
     * @param string|null $modelName
     * @return ControllerInterface
     * @throws Exception
     */
    public function initialiseModel(string $modelName = null): ControllerInterface
    {
        $response = parent::initialiseModel($modelName);

        $this->model->setVerb($this->verb);

        return $response;
    }

    /**
     * @param int|null $code
     * @param string|null $response
     */
    public function completeRender(int $code = null, string $response = null): void
    {
        parent::completeRender($code, $response);

        $this->saveCache($this->services, $code);
    }

    /**
     *
     */
    protected function parseUriParameters(): void {
        $uri = strtok($_SERVER['REQUEST_URI'] ?? '', '?');

        if (!(isset($uri) && $uri === '/')) {
            $variables = array_filter(explode('/', substr($uri, 1)), 'strlen');
            $variable = current($variables);
            if (stripos($variable, 'v') === 0 && is_numeric(substr($variable, 1, 1)) && strpos($variable, '.') !== 0){
                $this->version = $variable;
                array_shift($variables);
            }

            $this->passedParameters = $this->parseModelNameFromUri($variables);
        }
    }
}