<?php
declare(strict_types=1);
namespace Elephfront\RoboCssMinify\Task\Loader;

use Elephfront\RoboCssMinify\Task\CssMinify;

trait LoadCssMinifyTasksTrait
{
    
    /**
     * Exposes the ImportJavascript task.
     *
     * @param array $destinationMap Key / value pairs array where the key is the source and the value the destination.
     * @return \Elephfront\RoboCssMinify\Task\CssMinify Instance of the CssMinify Task
     */
    protected function taskCssMinify(array $destinationMap = [])
    {
        return $this->task(CssMinify::class, $destinationMap);
    }
}
