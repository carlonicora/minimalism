<?php
namespace CarloNicora\Minimalism\Commands;

use CarloNicora\Minimalism\Builders\ModelBuilder;
use CarloNicora\Minimalism\Services\Path;
use Exception;
use JsonException;

class ParametersReaderCommand
{
    /** @var string|null  */
    private ?string $modelName=null;

    /**
     * ParametersReaderCommand constructor.
     * @param Path $path
     * @param array|null $models
     */
    public function __construct(
        private Path $path,
        private ?array $models=null,
    ) {}

    /**
     * @return string|null
     */
    public function getModelName(): ?string
    {
        return $this->modelName;
    }

    /**
     * @return array
     */
    public function getCliParameters(): array
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
    public function getWebParameters(): array
    {
        $response = [];
        [$uri, $namedParametersString] = array_pad(
            explode('?', $_SERVER['REQUEST_URI'] ?? ''),
            2,
            ''
        );

        $this->path->sanitiseUriVersion($uri);

        if ($uri === '/'){
            $this->modelName = $this->models['*'];
        } else {
            $uriParts = explode('/', substr($uri, 1));
            $modelBuilder = new ModelBuilder($uriParts, $this->models, $this->path->getServicesModels());

            $this->modelName = $modelBuilder->getModel();
            $response['positioned'] = $modelBuilder->getParameters();

            unset($modelBuilder);
        }

        $response['named'] = $this->getNamedParameters($namedParametersString);

        return $response;
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
                    try {
                        $additionalResponse = [];
                        parse_str($phpInput, $additionalResponse);

                        if ($additionalResponse !== []) {
                            $response = array_merge($response, $additionalResponse);
                        }
                    } catch (Exception) {
                    }
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