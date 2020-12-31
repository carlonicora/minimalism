<?php
namespace CarloNicora\Minimalism\Factories;

use Exception;
use JetBrains\PhpStorm\ArrayShape;
use JsonException;
use RuntimeException;

class ParametersFactory
{
    /** @var string|null  */
    private ?string $modelName=null;

    /**
     * ParametersFactory constructor.
     * @param ServiceFactory $services
     * @param array $models
     */
    public function __construct(private ServiceFactory $services, private array $models)
    {}

    /**
     * @return string
     */
    public function getModelName(): string
    {
        return $this->modelName;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function createParameters(): array
    {
        if ($this->services->getPath()->getUrl() === null){
            return $this->getCliParameters();
        }

        return $this->getWebParameters();
    }

    /**
     * @return array
     */
    #[ArrayShape(['named' => "array"])]
    private function getCliParameters(): array
    {
        $response = [
            'named' => []
        ];

        $typeName = null;
        foreach ($_SERVER['argv'] ?? [] as $item) {
            if (str_starts_with($item, '-')){
                while (str_starts_with($item, '-')){
                    $item = substr($item, 1);
                }
                $typeName = $item;
            } elseif ($typeName !== null) {
                $response['named'][$typeName] = $item;
                $typeName = null;
            } else {
                try {
                    $response['named']['payload'] = json_decode($item, true, 512, JSON_THROW_ON_ERROR);
                } catch (JsonException) {
                }
            }
        }

        return $response;
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getWebParameters(): array
    {
        $response = [];
        [$uri, $namedParametersString] = array_pad(
            explode('?', $_SERVER['REQUEST_URI'] ?? ''),
            2,
            ''
        );

        $this->services->getPath()->sanitiseUriVersion($uri);

        if ($uri === '/'){
            $this->modelName = $this->models['*'];
        } else {
            $response['positioned'] = $this->loadPositionedParameters($uri);
        }

        $response['named'] = $this->getNamedParameters($namedParametersString);

        return $response;
    }

    /**
     * @param string $uri
     * @param array|null $models
     * @return array
     */
    private function loadPositionedParameters(string $uri, array $models=null): array
    {
        $searchServicesModels = false;
        if ($models === null){
            $models = $this->models;
            $searchServicesModels = true;
        }

        $response = [];

        $uriParts = explode('/', substr($uri, 1));

        $nestingLevel = intdiv(count($uriParts), 2);

        while ($nestingLevel >= 0){
            if (($modelName = $this->doesModelExists($uriParts, $nestingLevel, $models)) !== null){
                $this->modelName = $modelName;

                foreach ($uriParts as $position=>$parameter){
                    if ($position>=$nestingLevel*2 || ($position<$nestingLevel*2 && $position % 2 !== 0)){
                        $response[] = $parameter;
                    }
                }

                return $response;
            }

            $nestingLevel--;
        }

        if ($searchServicesModels) {
            try {
                foreach ($this->services->getPath()->getServicesModels() ?? [] as $additionalModels) {
                    $response = $this->loadPositionedParameters($uri, $additionalModels);

                    if ($this->modelName !== null) {
                        return $response;
                    }
                }
            } catch (Exception) {
            }
        }

        if (array_key_exists('*', $models)){
            $this->modelName = $models['*'];
            foreach ($uriParts as $parameter){
                $response[] = $parameter;
            }
            return $response;
        }

        throw new RuntimeException('Model not found', 404);
    }

    /**
     * @param array $uriParts
     * @param int $nestingLevel
     * @param array $models
     * @return string|null
     */
    private function doesModelExists(array $uriParts, int $nestingLevel, array $models): ?string
    {
        $currentModelPosition = $models;
        for ($position=0; $position<=$nestingLevel * 2; $position += 2){
            if ($position + 2 >= $nestingLevel * 2){
                return $currentModelPosition[strtolower($uriParts[$position])] ?? null;
            }

            if (array_key_exists(strtolower($uriParts[$position]) . '-folder', $currentModelPosition)){
                $currentModelPosition = $currentModelPosition[strtolower($uriParts[$position]) . '-folder'];
            } else {
                return null;
            }
        }

        return null;
    }

    /**
     * @param string|null $namedParametersString
     * @return array
     */
    private function getNamedParameters(?string $namedParametersString): array
    {
        $response = [];
        if ($namedParametersString !== null && $namedParametersString !== '') {
            $namedParameters = explode('&', $namedParametersString);
            foreach ($namedParameters ?? [] as $namedParameter) {
                [$parameterName, $parameterValue] = explode('=', $namedParameter);
                $response[$parameterName] = $parameterValue;
            }
        }

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET'){
            foreach ($_GET as $parameter => $value) {
                $response[$parameter] = $value;
            }
        } else {
            if (!empty($phpInput=file_get_contents('php://input'))) {
                try {
                    $response['payload'] = json_decode($phpInput, true, 512, JSON_THROW_ON_ERROR);
                } catch (Exception) {
                }
            }

            $response['files'] = $this->reArrayFiles($_FILES);

            foreach ($_POST as $parameter => $value) {
                $response[$parameter] = $value;
            }
        }

        return $response;
    }

    /**
     * @param array $files
     * @return array
     */
    private function reArrayFiles(array $files): array
    {
        $result = [];
        if (empty($files)) {
            return $result;
        }

        foreach ($files as $key => $file) {
            if (is_string($file['name'])) {
                $result[$key] = $file;
            } elseif (is_array($file['name'])) {
                $result[$key] = [];
                foreach ($file as $lastKey => $value1) {
                    $result[$key] = array_replace_recursive($result[$key], $this->recursive($lastKey, $file[$lastKey]));
                }
            }

        }

        return $result;
    }

    /**
     * @param $lastKey
     * @param $input
     * @return array
     */
    private function recursive($lastKey, $input): array
    {
        $result = [];
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->recursive($lastKey, $value);
            } else {
                $result[$key][$lastKey] = $value;
            }
        }

        return $result;
    }
}