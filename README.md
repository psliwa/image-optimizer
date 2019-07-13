# Image Optimizer [![Build Status](https://travis-ci.org/psliwa/image-optimizer.svg?branch=master)](https://travis-ci.org/psliwa/image-optimizer)

This library is handy and very easy to use optimizer for image files. It uses [optipng][2], [pngquant][1], [jpegoptim][6], [svgo][9] and few more libraries,
so before use it you should install proper libraries on your server. Project contains Vagrantfile that defines testing
virtual machine with all libraries installed, so you can check Vagrantfile how to install all those stuff.

Thanks to ImageOptimizer and libraries that it uses, your image files can be **10%-70% smaller**.

# Installation

Using composer:

    composer require ps/image-optimizer

# Basic usage

```php
$factory = new \ImageOptimizer\OptimizerFactory();
$optimizer = $factory->get();

$filepath = /* path to image */;

$optimizer->optimize($filepath);
//optimized file overwrites original one
```

# Configuration

By default optimizer does not throw any exception, if file can not be optimized or optimizing library for given file is
not installed, optimizer will not touch original file. This behaviour is ok when you want to eventually optimize files
uploaded by user. When in your use case optimization fault should cause exception, `ignore_errors` option was created
especially for you.

This library is very smart, you do not have to configure paths to all binaries of libraries that are used by ImageOptimizer,
library will be looking for those binaries in few places, so if binaries are placed in standard places, it will be found
automatically.

Supported options:

* `ignore_errors` (default: true)
* `single_optimizer_timeout_in_seconds` (default: 60) - useful when you
  want to have control how long optimizing lasts. For example in some
  cases optimizing may not be worth when it takes big amount of time.
  Pass `null` in order to turn off timeout.
* `output_filepath_pattern` (default: `%basename%/%filename%%ext%`) -
  destination where optimized file will be stored. By default it
  overrides original file. There are 3 placehoders: `%basename%`,
  `%filename%` (without extension and dot) and `%ext%` (extension with
  dot) which will be replaced by values from original file.
* `execute_only_first_png_optimizer` (default: true) - execute the first
  successful or all `png` optimizers
* `execute_only_first_jpeg_optimizer` (default: true) - execute the first successful or all `jpeg` optimizers
* `optipng_options` (default: `array('-i0', '-o2', '-quiet')`) - an array of arguments to pass to the library
* `pngquant_options` (default: `array('--force')`)
* `pngcrush_options` (default: `array('-reduce', '-q', '-ow')`)
* `pngout_options` (default: `array('-s3', '-q', '-y')`)
* `advpng_options` (default: `array('-z', '-4', '-q')`)
* `gifsicle_options` (default: `array('-b', '-O5')`)
* `jpegoptim_options` (default: `array('--strip-all', '--all-progressive')`)
* `jpegtran_options` (default: `array('-optimize', '-progressive')`)
* `svgo_options` (default: `array('--disable=cleanupIDs')`)
* `custom_optimizers` (default `array()`)
* `optipng_bin` (default: will be guessed) - you can enforce paths to binaries, but by default it will be guessed
* `pngquant_bin`
* `pngcrush_bin`
* `pngout_bin`
* `advpng_bin`
* `gifsicle_bin`
* `jpegoptim_bin`
* `jpegtran_bin`
* `svgo_bin`

You can pass array of options as first argument of `ImageOptimizer\OptimizerFactory` constructor. Second argument is
optionally `Psr\LoggerInterface`.

```php
$factory = new \ImageOptimizer\OptimizerFactory(array('ignore_errors' => false), $logger);
```

# Supported optimizers

* default (`smart`) - it guess file type and choose optimizer for this file type
* `png` - chain of optimizers for png files, by default it uses `pngquant` and `optipng`. `pngquant` is lossy optimization
* `jpg` - first of two optimizations will be executed: `jpegtran` or `jpegoptim`
* `gif` - alias to `gifsicle`
* `pngquant` - [homepage][1]
* `optipng` - [homepage][2]
* `pngcrush` - [homepage][3]
* `pngout` - [homepage][4]
* `advpng` - [homepage][5]
* `jpegtran` - [homepage][6]
* `jpegoptim` - [homepage][7]
* `gifsicle` - [homepage][8]
* `svgo` - [homepage][9]

You can obtain concrete optimizer by passing his name to `ImageOptimizer\OptimizerFactory`::`get` method:

```php
//default optimizer is `smart`
$optimizer = $factory->get();

//png optimizer
$pngOptimizer = $factory->get('png');

//jpegoptim optimizer etc.
$jpgOptimizer = $factory->get('jpegoptim');
```

# Custom optimizers

You can easily define custom optimizers:

```php
$factory = new \ImageOptimizer\OptimizerFactory(array('custom_optimizers' => array(
    'some_optimizier' => array(
        'command' => 'some_command',
        'args' => array('-some-flag')
    )
)), $logger);
```

And then usage:

```php
$customOptimizer = $factory->get('some_optimizier');
```

# I got "All optimizers failed to optimize the file"

Probably you don't have required optimazers installed. Let's have a look
at `Vagrantfile` file in order to see an example how to install those
commands.

In order to see all intermediate errors, you can use logger (be default
`NullLogger` is used, so logs are not available):

```php
class StdoutLogger extends \Psr\Log\AbstractLogger { 
    public function log($level, $message, array $context = array()) { 
        echo $message."\n"; 
    }
}

$factory = new \ImageOptimizer\OptimizerFactory(array(), new StdoutLogger());

$factory->get()->optimize('yourfile.jpg');

// and have a look at stdout
```

# License

**MIT**

[1]: http://pngquant.org/
[2]: http://optipng.sourceforge.net/
[3]: http://pmt.sourceforge.net/pngcrush/
[4]: http://www.jonof.id.au/kenutils
[5]: http://advancemame.sourceforge.net/doc-advpng.html
[6]: http://jpegclub.org/jpegtran/
[7]: http://freecode.com/projects/jpegoptim
[8]: http://www.lcdf.org/gifsicle/
[9]: https://github.com/svg/svgo
