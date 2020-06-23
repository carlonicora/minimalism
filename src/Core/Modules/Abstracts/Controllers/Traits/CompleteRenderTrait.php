<?php
namespace CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers\Traits;

use CarloNicora\Minimalism\Core\Events\MinimalismErrorEvents;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use Exception;
use function file_put_contents;
use function serialize;
/**
 * @property ServicesFactory $services
 */

trait CompleteRenderTrait
{
    /**
     * @param ServicesFactory $services
     * @param int|null $code
     * @throws Exception
     */
    public function saveCache(ServicesFactory $services, int $code = null): void
    {
        if ((int)$code < 400 && $services->paths()->getCache() !== null){
            try {
                $this->persistAtPath($services->paths()->getCache(), serialize($services));
            } catch (Exception $exception) {
                $services->logger()->error()
                    ->log(MinimalismErrorEvents::SERVICE_CACHE_ERROR($exception));
            }
        }
    }


    /**
     * Wrapper around file_put_contents that allows the saveCache method to be unit tested
     * @internal
     * @param $filepath
     * @param $content
     * @throws Exception
     */
    public function persistAtPath($filepath, $content): void
    {
        if (false === file_put_contents($filepath, $content)) {
            $this->services->logger()->error()->log(
                MinimalismErrorEvents::FILE_WRITE_ERROR($filepath)
            )->throw();
        }
    }
}
