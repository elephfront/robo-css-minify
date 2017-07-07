<?php
namespace Elephfront\RoboCssMinify\Task\Loader;

use Elephfront\RoboCssMinify\Task\CssMinify;

trait CssMinifyLoader
{
    
    /**
     * Exposes the ImportJavascript task.
     *
     * @param array $destinationMap Key / value pairs array where the key is the source and the value the destination.
     * @return \Elephfront\RoboCssMinify\Task\CssMinify Instance of the CssMinify Task
     */
    protected function taskCssMinify($destinationMap = [])
    {
        return $this->task(CssMinify::class, $destinationMap);
    }
}