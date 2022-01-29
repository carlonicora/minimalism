<?php /** @noinspection PhpExpressionResultUnusedInspection */

namespace CarloNicora\Minimalism\Tests\Abstracts;

use CarloNicora\Minimalism\Minimalism;
use CarloNicora\Minimalism\Services\Path;
use CarloNicora\Minimalism\Tests\Factories\MocksFactory;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

abstract class AbstractTestCase extends TestCase
{
    /** @var MocksFactory */
    protected MocksFactory $mocker;

    /** @var Minimalism|null  */
    private ?Minimalism $minimalism=null;

    /**
     * @return void
     */
    protected function setUp(
    ): void
    {
        parent::setUp();

        self::deleteAllFilesInFolder(__DIR__ . '/../../cache');

        $this->mocker = new MocksFactory($this);
    }

    /**
     * @param string $dir
     * @return bool
     */
    public static function recurseRmdir(
        string $dir
    ): bool
    {
        if(!is_dir($dir)) {
            return true;
        }

        $files = array_diff(scandir($dir), array('.','..'));

        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? self::recurseRmdir("$dir/$file") : unlink("$dir/$file");
        }

        return rmdir($dir);
    }

    /**
     * @return string
     */
    public function createTmpDir(
    ): string
    {
        if (!file_exists(sys_get_temp_dir())){
            mkdir(sys_get_temp_dir());
        }

        $tmpDir = sys_get_temp_dir().'/tmp';

        if (!file_exists($tmpDir)) {
            mkdir($tmpDir);
        }

        return $tmpDir;
    }

    /**
     * @param string $dir
     * @param bool $recursive
     */
    private static function deleteAllFilesInFolder(
        string $dir,
        bool $recursive=true,
    ): void
    {
        foreach(glob($dir . '/*') as $file) {
            if(is_file($file)) {
                unlink($file);
            } elseif ($recursive){
                self::deleteAllFilesInFolder($file);
                rmdir($file);
            }
        }
    }

    /**
     * @return Minimalism
     */
    protected function generateMinimalism(
    ): Minimalism
    {
        if ($this->minimalism === null) {
            $this->minimalism = new Minimalism();
        }

        return $this->minimalism;
    }

    /**
     * @param $object
     * @param $parameterName
     * @return mixed
     */
    protected function getProperty($object, $parameterName): mixed
    {
        try {
            $property = (new ReflectionClass(get_class($object)))->getProperty($parameterName);
            $property->setAccessible(true);
            return $property->getValue($object);
        } catch (ReflectionException) {
            return null;
        }
    }

    /**
     * @param $object
     * @param $parameterName
     * @param $parameterValue
     */
    protected function setProperty($object, $parameterName, $parameterValue): void
    {
        try {
            $property = (new ReflectionClass(get_class($object)))->getProperty($parameterName);
            $property->setAccessible(true);
            $property->setValue($object, $parameterValue);
        } catch (ReflectionException) {
        }
    }

    /**
     * @param object $object
     * @param string $methodName
     * @param array $arguments
     * @return mixed
     * @throws ReflectionException
     */
    protected function invokeMethod(object &$object, string $methodName, array $arguments = [])
    {
        $reflection = new ReflectionClass($object::class);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $arguments);
    }
}