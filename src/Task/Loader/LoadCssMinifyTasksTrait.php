<?php
/**
 * Copyright (c) Yves Piquel (http://www.havokinspiration.fr)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Yves Piquel (http://www.havokinspiration.fr)
 * @link          http://github.com/elephfront/robo-css-minify
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
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
