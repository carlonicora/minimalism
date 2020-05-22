<?php
namespace CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers\Traits\CompleteRenderTrait;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ControllerInterface;
use JsonException;

abstract class AbstractCliController extends AbstractController
{
    use CompleteRenderTrait;

    /**
     * @param array $parameterValueList
     * @param array $parameterValues
     * @return ControllerInterface
     */
    public function initialiseParameters(array $parameterValueList=[], array $parameterValues=[]): ControllerInterface
    {
        $typeName = null;
        foreach ($_SERVER['argv'] ?? [] as $item) {
            if (strpos($item, '-') === 0){
                while (strpos($item, '-') === 0){
                    $item = substr($item, 1);
                }
                $typeName = $item;
            } elseif ($typeName !== null) {
                $this->passedParameters[$typeName] = $item;
                $typeName = null;
            } else {
                try {
                    $this->passedParameters = json_decode($item, true, 512, JSON_THROW_ON_ERROR);
                } catch (JsonException $e) {
                }
            }
        }

        return $this;
    }

    /**
     * @return ControllerInterface
     */
    public function postInitialise(): ControllerInterface
    {
        return $this;
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
}