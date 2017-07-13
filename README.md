# Robo CSS Minify

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?branch=master)](LICENSE.txt)
[![Build Status](https://travis-ci.org/elephfront/robo-css-minify.svg?branch=master)](https://travis-ci.org/elephfront/robo-css-minify)
[![Codecov](https://img.shields.io/codecov/c/github/elephfront/robo-css-minify.svg)](https://github.com/elephfront/robo-css-minify)

This [Robo](https://github.com/consolidation/robo) task performs a minification of your CSS content.

This task performs the minification using [matthiasmullie/minify](https://github.com/matthiasmullie/minify) library.

## Requirements

- PHP >= 7.1.0
- Robo

## Installation

You can install this Robo task using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require elephfront/robo-css-minify
```

## Using the task

You can load the task in your RoboFile using the `LoadCssMinifyTasksTrait` trait:

```php
use Elephfront\RoboCssMinify\Task\Loader\LoadCssMinifyTasksTrait;

class RoboFile extends Tasks
{

    use LoadCssMinifyTasksTrait;
    
    public function minifyCss()
    {
        $this
            ->taskCssMinify([
                'assets/css/main.css' => 'assets/min/css/main.min.css',
                'assets/css/home.css' => 'assets/min/css/home.min.css',
            ])
            ->run();
    }
}
```

The only argument the `taskCssMinify()` takes is an array (`$destinationsMap`) which maps the source files to the destination files : it will load the **assets/css/main.css**, do its magic and put the final content in **assets/min/css/main.min.css** and do the same for all of the other files.

## GZIP compression

In addition to minifying your files, you can gzip them. You can enable gzip using the `enableGzip()` method :

```php
    $this
        ->taskCssMinify([
            'assets/css/main.css' => 'assets/min/css/main.min.css',
            'assets/css/home.css' => 'assets/min/css/home.min.css',
        ])
        ->enableGzip()
        ->run();
```

By default, files will be compressed using the maximum level (which is 9). You can customize the compression level using the method `setGzipLevel()` :

```php
    $this
        ->taskCssMinify([
            'assets/css/main.css' => 'assets/min/css/main.min.css',
            'assets/css/home.css' => 'assets/min/css/home.min.css',
        ])
        ->enableGzip()
        ->setGzipLevel(5)
        ->run();
```

The `setGzipLevel` accepts values from `-1` to `9`. If you use `-1`, the default compression level of the zlib library will be used.
`0` means you do not want any compression and `9` is the maximum level of compression.
If you use a value that is out of these bounds, the maximum compression level will be used.

## Embedding files

The **robo-css-minify** can embed files directly into the minified CSS. By default, no files will be embedded. You can enable this feature by specifying to the task the maximum size the files (in kB) should have to be imported by using the `setMaxImportSize()` method: 

```php
    $this
        ->taskCssMinify([
            'assets/css/main.css' => 'assets/min/css/main.min.css',
            'assets/css/home.css' => 'assets/min/css/home.min.css',
        ])
        ->setMaxImportSize(5)
        ->run();
```

The above code will import as a data string all files that are less that 5kB directly into the minified CSS.

You can also filter the extensions the files should have. By default, if you specify a size, all files having an extension in the following list will be processed (and imported if the size match) :

- gif
- png
- jpe
- jpg
- jpeg
- svg
- woff
- tif
- tiff
- xbm

If you wish to control which extensions should be imported, you can use the `setImportExtensions()` method :

```php
    $this
        ->taskCssMinify([
            'assets/css/main.css' => 'assets/min/css/main.min.css',
            'assets/css/home.css' => 'assets/min/css/home.min.css',
        ])
        ->setMaxImportSize(5)
        ->setImportExtensions(['jpg', 'png'])
        ->run();
```

The above code will only import files that have the `jpg` or `png` extension and that are less that 5kB.

Please note that the only supported file extensions are the one from the list above. 

## Chained State support

Robo includes a concept called the [Chained State](http://robo.li/collections/#chained-state) that allows tasks that need to work together to be executed in a sequence and pass the state of the execution of a task to the next one.
For instance, if you are managing assets files, you will have a task that compile SCSS to CSS then another one that minify the results. The first task can pass the state of its work to the next one, without having to call both methods in a separate sequence.

The **robo-css-minify** task is compatible with this feature.

All you need to do is make the previous task return the content the **robo-css-minify** task should operate on using the `data` argument of a `Robo\Result::success()` or `Robo\Result::error()` call. The passed `data` should have the following format:
 
```php
$data = [
    'path/to/source/file' => [
        'css' => '// Some CSS code',
        'destination' => 'path/to/destination/file
    ]
];
```

In turn, when the **robo-css-minify** task is done, it will pass the results of its work to the next task following the same format.

## Preventing the results from being written

By default, the **robo-css-minify** task writes the result of its work into the destination file(s) passed in the `$destinationsMap` argument. If the **robo-css-minify** task is not the last one in the sequence, you can disable the file writing using the `disableWriteFile()` method. The files will be processed but the results will not be persisted and only passed to the response :

```php
$this
    ->taskCssMinify([
        'assets/js/main.css' => 'assets/min/css/main.min.css',
        'assets/js/home.css' => 'assets/min/css/home.min.css',
    ])
        ->disableWriteFile()
    ->someOtherTask()
    ->run();
```

## Contributing

If you find a bug or would like to ask for a feature, please use the [GitHub issue tracker](https://github.com/Elephfront/robo-css-minify/issues).
If you would like to submit a fix or a feature, please fork the repository and [submit a pull request](https://github.com/Elephfront/robo-css-minify/pulls).

### Coding standards

This repository follows the PSR-2 standard. 

## License

Copyright (c) 2017, Yves Piquel and licensed under [The MIT License](http://opensource.org/licenses/mit-license.php).
Please refer to the LICENSE.txt file.
