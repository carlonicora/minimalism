<?php
namespace CarloNicora\Minimalism\Tests\Unit\Mocks\Models;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Models\AbstractModel;

class SecondGenericModel extends AbstractModel
{
    /** @var string  */
    protected ?string $redirectPage = 'GenericModel';

    public function preRender(): void
    {
    }
}