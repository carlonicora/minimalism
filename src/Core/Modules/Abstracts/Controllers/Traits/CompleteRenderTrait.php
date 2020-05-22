<?php
namespace CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers\Traits;

use CarloNicora\Minimalism\Core\Events\MinimalismErrorEvents;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use Exception;

trait CompleteRenderTrait
{
    /**
     * @param ServicesFactory $services
     * @param int|null $code
     */
    public function saveCache(ServicesFactory $services, int $code = null): void
    {
        if ((int)$code < 400 && $services->paths()->getCache() !== null){
            try {
                file_put_contents($services->paths()->getCache(), serialize($services));
            } catch (Exception $exception) {
                $services->logger()->error()
                    ->log(MinimalismErrorEvents::SERVICE_CACHE_ERROR($exception));
            }
        }
    }
}