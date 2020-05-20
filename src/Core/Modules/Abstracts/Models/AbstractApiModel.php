<?php
namespace CarloNicora\Minimalism\Core\Modules\Abstracts\Models;

use CarloNicora\Minimalism\Core\Modules\Interfaces\ApiModelInterface;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use CarloNicora\Minimalism\Core\Response;

abstract class AbstractApiModel extends AbstractModel implements ApiModelInterface
{
    /** @var bool */
    protected bool $requiresAuthDELETE=false;

    /** @var bool */
    protected bool $requiresAuthGET=false;

    /** @var bool */
    protected bool $requiresAuthPOST=false;

    /** @var bool */
    protected bool $requiresAuthPUT=false;

    /** @var string */
    public string $verb='GET';

    public function setVerb(string $verb) : void
    {
        $this->verb = $verb;
    }

    /**
     * @param $verb
     * @return mixed
     */
    public function requiresAuth($verb): bool
    {
        $authName = 'requiresAuth' . $verb;

        return $this->$authName;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        if (array_key_exists($this->verb, $this->parameters)){
            return $this->parameters[$this->verb];
        }

        return [];
    }

    /**
     * @return Response
     */
    abstract public function DELETE() : ResponseInterface;

    /**
     * @return Response
     */
    abstract public function GET() : ResponseInterface;

    /**
     * @return Response
     */
    abstract public function POST() : ResponseInterface;

    /**
     * @return Response
     */
    abstract public function PUT() : ResponseInterface;
}