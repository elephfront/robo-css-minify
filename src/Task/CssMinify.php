<?php
namespace Elephfront\RoboCssMinify\Task;

use InvalidArgumentException;
use MatthiasMullie\Minify\CSS;
use Robo\Contract\TaskInterface;
use Robo\Result;
use Robo\State\Consumer;
use Robo\State\Data;
use Robo\Task\BaseTask;

/**
 * Class CssMinify
 *
 * Consider that this task should be the last to run as the destination files will be written by it.
 */
class CssMinify extends BaseTask implements TaskInterface, Consumer
{

    /**
     * List of the destinations files mapped by the sources name. One source equals one destination.
     *
     * @var array
     */
    protected $destinationsMap = [];

    /**
     * Instance of the CSS minifier object.
     *
     * @var \MatthiasMullie\Minify\CSS
     */
    protected $minifier;

    /**
     * Data that was received from the previous task.
     * This array can stay empty if this task if the first to be run.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Data that will be passed to the next task with the Robo response.
     *
     * @var array
     */
    protected $returnData = [];

    /**
     * Constructor. Will bind the destinations map.
     *
     * @param array $destinationsMap Key / value pairs array where the key is the source and the value the destination.
     */
    public function __construct(array $destinationsMap = [])
    {
        $this->setDestinationsMap($destinationsMap);
    }

    /**
     * Sets the destinations map.
     *
     * @param array $destinationsMap Key / value pairs array where the key is the source and the value the destination.
     * @return self
     */
    public function setDestinationsMap(array $destinationsMap = [])
    {
        $this->destinationsMap = $destinationsMap;

        return $this;
    }

    /**
     * Runs the tasks : will replace all import statements from the source files from the `self::$destinationsMap` and
     * write them to the destinations file from the `self::$destinationsMap`.
     *
     * @return \Robo\Result Result object from Robo
     * @throws \InvalidArgumentException If no destinations map has been found.
     */
    public function run()
    {
        if ($this->data) {
            $exec = $this->processInlineData($this->data);
        } else {
            if (empty($this->destinationsMap)) {
                throw new InvalidArgumentException(
                    'Impossible to run the CssMinify task without a destinations map.'
                );
            }

            $exec = $this->processDestinationsMap($this->destinationsMap);
        }

        if ($exec !== true) {
            return Result::error(
                $this,
                sprintf('An error occurred while writing the destination file for source file `%s`', $exec),
                $this->returnData
            );
        } else {
            return Result::success($this, 'All CSS has been minified.', $this->returnData);
        }
    }

    /**
     * Execute the CSSMinify if we are dealing with a source maps (key = source file / value = destination)
     *
     * @param array $destinationsMap List of the destinations files mapped by the sources name. One source equals one
     * destination.
     * @return bool|string True if everything went ok, error otherwise.
     */
    protected function processDestinationsMap($destinationsMap)
    {
        $exec = true;
        foreach ($destinationsMap as $source => $destination) {
            $this->minifier = new CSS();
            $this->minifier->add($source);
            $exec = $this->execMinify($source, $destination);

            unset($this->minifier);

            if ($exec !== true) {
                break;
            }
        }

        return $exec;
    }

    /**
     * Execute the CSSMinify if we are dealing with raw CSS content (from another task).
     *
     * @param array $data Key : source file. Value : array with two keys :
     * - *css* : raw CSS content to minify
     * - *destination* : the destination of the processed content.
     * @return bool|string True if everything went ok, error otherwise.
     */
    protected function processInlineData($data)
    {
        $exec = true;
        foreach ($data as $source => $content) {
            $this->minifier = new CSS();
            $css = $content['css'];
            $destination = $content['destination'];

            $this->minifier->add($css);
            $exec = $this->execMinify($source, $destination);

            unset($this->minifier);

            if ($exec !== true) {
                break;
            }
        }

        return $exec;
    }

    /**
     * Execute the CSS minification
     *
     * @param string $source Path of the source file.
     * @param string $destination Path of the destination file.
     * @return bool|string True if everything went ok, false if an error occurred.
     */
    protected function execMinify($source, $destination)
    {
        $destinationDirectory = dirname($destination);

        if (!is_dir($destinationDirectory)) {
            mkdir($destinationDirectory, 0755, true);
        }

        try {
            $css = $this->minifier->minify($destination);

            $this->printTaskSuccess(
                sprintf(
                    'Minified CSS from <info>%s</info> to <info>%s</info>',
                    $source,
                    $destination
                )
            );

            $this->returnData[$source] = ['css' => $css, 'destination' => $destination];
        } catch (\Exception $e) {
            $error = $e->getMessage();
            return $error;
        }

        return true;
    }

    /**
     * Gets the state from the previous task. Stores it in the `data` attribute of the object.
     * This method is called before the task is run.
     *
     * @param \Robo\State\Data $state State passed from the previous task.
     * @return void
     */
    public function receiveState(Data $state)
    {
        $this->data = $state->getData();
    }
}
