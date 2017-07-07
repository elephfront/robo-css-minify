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
        if (file_exists(TESTS_ROOT . 'app' . DS . 'js' . DS . 'output.css')) {
            unlink(TESTS_ROOT . 'app' . DS . 'js' . DS . 'output.css');
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
//    public function testInexistantSource()
//    {
//        $this->task->setDestinationsMap([
//            'bogus' => 'bogus'
//        ]);
//        $result = $this->task->run();
//
//        $this->assertInstanceOf(Result::class, $result);
//        $this->assertEquals(Result::EXITCODE_ERROR, $result->getExitCode());
//        $this->assertEquals(
//            'Impossible to find source file `bogus`',
//            $result->getMessage()
//        );
//    }
//
//    /**
//     * Test a basic import with a single import but with an inexistant imported file.
//     *
//     * @return void
//     */
//    public function testBasicImportWithInexistantImportedFile()
//    {
//        $basePath = TESTS_ROOT . 'app' . DS . 'js';
//        $this->task->setDestinationsMap([
//            $basePath . DS . 'simple-wrong.js' => $basePath . DS . 'output.js'
//        ]);
//        $result = $this->task->run();
//
//        $this->assertInstanceOf(Result::class, $result);
//        $this->assertEquals(Result::EXITCODE_ERROR, $result->getExitCode());
//        $this->assertEquals(
//            'Impossible to find imported file `' . $basePath . DS . 'imports' . DS . 'not-here.js`',
//            $result->getMessage()
//        );
//    }
//
//    /**
//     * Tests that the task returns an error in case the file can not be written if normal mode
//     *
//     * @return void
//     */
//    public function testImportError()
//    {
//        $basePath = TESTS_ROOT . 'app' . DS . 'js';
//        $this->task = $this->getMockBuilder(ImportJavascript::class)
//            ->setMethods(['writeFile'])
//            ->getMock();
//        $this->task->setLogger(new MemoryLogger());
//
//        $this->task->method('writeFile')
//            ->willReturn(false);
//
//        $data = new Data();
//        $data->mergeData([
//            TESTS_ROOT . 'app' . DS . 'js' . DS . 'simple.js' => [
//                'js' => 'roboimport(\'imports/bogus\');',
//                'destination' => TESTS_ROOT . 'app' . DS . 'js' . DS . 'output.js'
//            ]
//        ]);
//        $this->task->receiveState($data);
//        $result = $this->task->run();
//
//        $this->assertInstanceOf(Result::class, $result);
//        $this->assertEquals(Result::EXITCODE_ERROR, $result->getExitCode());
//
//        $log = 'An error occurred while writing the destination file for source file `' . $basePath . DS . 'simple.js`';
//        $this->assertEquals(
//            $log,
//            $result->getMessage()
//        );
//    }
//
//    /**
//     * Tests that the task returns an error in case the file can not be written in "chained state mode"
//     *
//     * @return void
//     */
//    public function testImportErrorChainedState()
//    {
//        $basePath = TESTS_ROOT . 'app' . DS . 'js';
//        $this->task = $this->getMockBuilder(ImportJavascript::class)
//            ->setMethods(['writeFile'])
//            ->getMock();
//        $this->task->setLogger(new MemoryLogger());
//
//        $this->task->method('writeFile')
//            ->willReturn(false);
//
//        $this->task->setDestinationsMap([
//            $basePath . DS . 'simple.js' => $basePath . DS . 'output.js'
//        ]);
//        $result = $this->task->run();
//
//        $this->assertInstanceOf(Result::class, $result);
//        $this->assertEquals(Result::EXITCODE_ERROR, $result->getExitCode());
//
//        $log = 'An error occurred while writing the destination file for source file `' . $basePath . DS . 'simple.js`';
//        $this->assertEquals(
//            $log,
//            $result->getMessage()
//        );
//    }
//
//    /**
//     * Test a basic import with a single import.
//     *
//     * @return void
//     */
//    public function testBasicImport()
//    {
//        $this->task->setDestinationsMap([
//            TESTS_ROOT . 'app' . DS . 'js' . DS . 'simple.js' => TESTS_ROOT . 'app' . DS . 'js' . DS . 'output.js'
//        ]);
//        $result = $this->task->run();
//
//        $this->assertInstanceOf(Result::class, $result);
//        $this->assertEquals(Result::EXITCODE_OK, $result->getExitCode());
//
//        $this->assertEquals(
//            file_get_contents(TESTS_ROOT . 'comparisons' . DS . __FUNCTION__ . '.js'),
//            file_get_contents(TESTS_ROOT . 'app' . DS . 'js' . DS . 'output.js')
//        );
//
//        $source = TESTS_ROOT . 'app' . DS . 'js' . DS . 'simple.js';
//        $dest = TESTS_ROOT . 'app' . DS . 'js' . DS . 'output.js';
//        $expectedLog = 'Replaced import statement from file <info>' . $source . '</info> to <info>' . $dest . '</info>';
//        $this->assertEquals(
//            $expectedLog,
//            $this->task->logger()->getLogs()[0]
//        );
//    }
//
//    /**
//     * Test an import with the writeFile feature disabled.
//     *
//     * @return void
//     */
//    public function testImportNoWrite()
//    {
//        $this->task->setDestinationsMap([
//            TESTS_ROOT . 'app' . DS . 'js' . DS . 'simple.js' => TESTS_ROOT . 'app' . DS . 'js' . DS . 'output.js'
//        ]);
//        $this->task->disableWriteFile();
//        $result = $this->task->run();
//
//        $this->assertInstanceOf(Result::class, $result);
//        $this->assertEquals(Result::EXITCODE_OK, $result->getExitCode());
//        
//        $this->assertFalse(file_exists(TESTS_ROOT . 'app' . DS . 'js' . DS . 'output.js'));
//
//        $source = TESTS_ROOT . 'app' . DS . 'js' . DS . 'simple.js';
//        $expectedLog = 'Replaced import statement from file <info>' . $source . '</info>';
//        $this->assertEquals(
//            $expectedLog,
//            $this->task->logger()->getLogs()[0]
//        );
//    }
//
//    /**
//     * Test a basic import with a nested import.
//     *
//     * @return void
//     */
//    public function testNestedImport()
//    {
//        $basePath = TESTS_ROOT . 'app' . DS . 'js' . DS;
//        $this->task->setDestinationsMap([
//            $basePath . 'nested.js' => $basePath . 'deep' . DS . 'output.js'
//        ]);
//        $result = $this->task->run();
//
//        $this->assertInstanceOf(Result::class, $result);
//        $this->assertEquals(Result::EXITCODE_OK, $result->getExitCode());
//
//        $this->assertEquals(
//            file_get_contents(TESTS_ROOT . 'comparisons' . DS . __FUNCTION__ . '.js'),
//            file_get_contents($basePath . 'deep' . DS . 'output.js')
//        );
//    }
//
//    /**
//     * Test a basic import using the chained state.
//     *
//     * @return void
//     */
//    public function testImportWithChainedState()
//    {
//        $data = new Data();
//        $data->mergeData([
//            TESTS_ROOT . 'app' . DS . 'js' . DS . 'simple.js' => [
//                'js' => 'roboimport(\'imports/bogus\');',
//                'destination' => TESTS_ROOT . 'app' . DS . 'js' . DS . 'output.js'
//            ]
//        ]);
//        $this->task->receiveState($data);
//        $result = $this->task->run();
//
//        $this->assertInstanceOf(Result::class, $result);
//        $this->assertEquals(Result::EXITCODE_OK, $result->getExitCode());
//        
//        $resultData = $result->getData();
//        $expected = [
//            TESTS_ROOT . 'app' . DS . 'js' . DS . 'simple.js' => [
//                'js' => '// Some bogus JS code goes here',
//                'destination' => TESTS_ROOT . 'app' . DS . 'js' . DS . 'output.js'
//            ]
//        ];
//
//        $this->assertTrue(is_array($resultData));
//        $this->assertEquals($expected, $resultData);
//    }
//
//    /**
//     * Test an import with a source map containing multiple files.
//     *
//     * @return void
//     */
//    public function testMultipleSourcesImport()
//    {
//        $basePath = TESTS_ROOT . 'app' . DS . 'js' . DS;
//        $desinationsMap = [
//            $basePath . 'simple.js' => $basePath . 'output.js',
//            $basePath . 'nested.js' => $basePath . 'output-nested.js'
//        ];
//
//        $comparisonsMap = [
//            $basePath . 'simple.js' => TESTS_ROOT . 'comparisons' . DS . 'testBasicImport.js',
//            $basePath . 'nested.js' => TESTS_ROOT . 'comparisons' . DS . 'testNestedImport.js'
//        ];
//
//        $this->task->setDestinationsMap($desinationsMap);
//        $result = $this->task->run();
//
//        $this->assertInstanceOf(Result::class, $result);
//        $this->assertEquals(Result::EXITCODE_OK, $result->getExitCode());
//
//        foreach ($desinationsMap as $source => $destination) {
//            $this->assertEquals(
//                file_get_contents($comparisonsMap[$source]),
//                file_get_contents($destination)
//            );
//        }
//
//        $sentenceStart = 'Replaced import statement from file';
//
//        $source = $basePath . 'simple.js';
//        $destination = $basePath . 'output.js';
//        $expectedLog = $sentenceStart . ' <info>' . $source . '</info> to <info>' . $destination . '</info>';
//        $this->assertEquals(
//            $expectedLog,
//            $this->task->logger()->getLogs()[0]
//        );
//
//        $source = TESTS_ROOT . 'app' . DS . 'js' . DS . 'nested.js';
//        $destination = TESTS_ROOT . 'app' . DS . 'js' . DS . 'output-nested.js';
//        $expectedLog = $sentenceStart . ' <info>' . $source . '</info> to <info>' . $destination . '</info>';
//        $this->assertEquals(
//            $expectedLog,
//            $this->task->logger()->getLogs()[1]
//        );
//    }
}
