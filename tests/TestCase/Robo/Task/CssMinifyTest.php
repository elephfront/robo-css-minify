<?php
namespace Elephfront\RoboCssMinify\Tests;

use Elephfront\RoboCssMinify\Task\CssMinify;
use Elephfront\RoboCssMinify\Tests\Utility\MemoryLogger;
use PHPUnit\Framework\TestCase;
use Robo\Result;
use Robo\Robo;
use Robo\State\Data;

/**
 * Class CssMinifyTest
 *
 * Test cases for the CssMinify Robo task.
 */
class CssMinifyTest extends TestCase
{

    /**
     * Instance of the task that will be tested.
     *
     * @var \Elephfront\RoboCssMinify\Task\CssMinify
     */
    protected $task;

    /**
     * setUp.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        Robo::setContainer(Robo::createDefaultContainer());
        $this->task = new CssMinify();
        $this->task->setLogger(new MemoryLogger());
        if (file_exists(TESTS_ROOT . 'app' . DS . 'css' . DS . 'output.css')) {
            unlink(TESTS_ROOT . 'app' . DS . 'css' . DS . 'output.css');
        }
        if (file_exists(TESTS_ROOT . 'app' . DS . 'css' . DS . 'output-complex.css')) {
            unlink(TESTS_ROOT . 'app' . DS . 'css' . DS . 'output-complex.css');
        }
    }

    /**
     * tearDown.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->task);
    }

    /**
     * Tests that giving the task no destinations map will throw an exception.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Impossible to run the CssMinify task without a destinations map.
     * @return void
     */
    public function testNoDestinationsMap()
    {
        $this->task->run();
    }

    /**
     * Tests that giving the task a destinations map with an invalid source file will throw an exception.
     *
     * @return void
     */
    public function testInexistantSource()
    {
        $this->task->setDestinationsMap([
            'bogus' => 'bogus'
        ]);
        $result = $this->task->run();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(Result::EXITCODE_ERROR, $result->getExitCode());
        $this->assertEquals(
            'Impossible to find source file `bogus`',
            $result->getMessage()
        );
    }

    /**
     * Test a basic minification (with a set source map)
     *
     * @return void
     */
    public function testBasicMinification()
    {
        $basePath = TESTS_ROOT . 'app' . DS . 'css' . DS;
        $this->task->setDestinationsMap([
            $basePath . 'simple.css' => $basePath . 'output.css'
        ]);
        $result = $this->task->run();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(Result::EXITCODE_OK, $result->getExitCode());

        $this->assertEquals(
            file_get_contents(TESTS_ROOT . 'comparisons' . DS . __FUNCTION__ . '.css'),
            file_get_contents($basePath . 'output.css')
        );

        $source = $basePath . 'simple.css';
        $dest = $basePath . 'output.css';
        $expectedLog = 'Minified CSS from <info>' . $source . '</info> to <info>' . $dest . '</info>';
        $this->assertEquals(
            $expectedLog,
            $this->task->logger()->getLogs()[0]
        );
    }

    /**
     * Test an import with the writeFile feature disabled.
     *
     * @return void
     */
    public function testImportNoWrite()
    {
        $basePath = TESTS_ROOT . 'app' . DS . 'css' . DS;
        $this->task->setDestinationsMap([
            $basePath . 'simple.css' => $basePath . 'output.css'
        ]);
        $this->task->disableWriteFile();
        $result = $this->task->run();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(Result::EXITCODE_OK, $result->getExitCode());
        
        $this->assertFalse(file_exists($basePath . 'output.css'));

        $source = $basePath . 'simple.css';
        $expectedLog = 'Minified CSS from <info>' . $source . '</info>';
        $this->assertEquals(
            $expectedLog,
            $this->task->logger()->getLogs()[0]
        );
    }

    /**
     * Test a basic import using the chained state.
     *
     * @return void
     */
    public function testImportWithChainedState()
    {
        $basePath = TESTS_ROOT . 'app' . DS . 'css' . DS;
        $data = new Data();
        $data->mergeData([
            $basePath . 'simple.css' => [
                'css' => "body {\n\tbackground-color: red;\n}",
                'destination' => $basePath . 'output.css'
            ]
        ]);
        $this->task->receiveState($data);
        $result = $this->task->run();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(Result::EXITCODE_OK, $result->getExitCode());
        
        $resultData = $result->getData();
        $expected = [
            $basePath . 'simple.css' => [
                'css' => 'body{background-color:red}',
                'destination' => $basePath . 'output.css'
            ]
        ];

        $this->assertTrue(is_array($resultData));
        $this->assertEquals($expected, $resultData);
    }

    /**
     * Test an import with a source map containing multiple files.
     *
     * @return void
     */
    public function testMultipleSourcesImport()
    {
        $basePath = TESTS_ROOT . 'app' . DS . 'css' . DS;
        $desinationsMap = [
            $basePath . 'simple.css' => $basePath . 'output.css',
            $basePath . 'more-complex.css' => $basePath . 'output-complex.css'
        ];

        $comparisonsMap = [
            $basePath . 'simple.css' => TESTS_ROOT . 'comparisons' . DS . 'testBasicMinification.css',
            $basePath . 'more-complex.css' => TESTS_ROOT . 'comparisons' . DS . 'testComplexMinification.css'
        ];

        $this->task->setDestinationsMap($desinationsMap);
        $result = $this->task->run();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(Result::EXITCODE_OK, $result->getExitCode());

        foreach ($desinationsMap as $source => $destination) {
            $this->assertEquals(
                file_get_contents($comparisonsMap[$source]),
                file_get_contents($destination)
            );
        }

        $sentenceStart = 'Minified CSS from';

        $source = $basePath . 'simple.css';
        $destination = $basePath . 'output.css';
        $expectedLog = $sentenceStart . ' <info>' . $source . '</info> to <info>' . $destination . '</info>';
        $this->assertEquals(
            $expectedLog,
            $this->task->logger()->getLogs()[0]
        );

        $source = TESTS_ROOT . 'app' . DS . 'css' . DS . 'more-complex.css';
        $destination = TESTS_ROOT . 'app' . DS . 'css' . DS . 'output-complex.css';
        $expectedLog = $sentenceStart . ' <info>' . $source . '</info> to <info>' . $destination . '</info>';
        $this->assertEquals(
            $expectedLog,
            $this->task->logger()->getLogs()[1]
        );
    }
}
