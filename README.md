# Image Optimizer

This library is handy and very easy to use optimizer for image files. It uses [optipng][2], [pngquant][1], [jpegoptim][6] and few more libraries,
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
* `execute_only_first_png_optimizer` (default: true) - execute the first successful or all `png` optimizers
* `execute_only_first_jpeg_optimizer` (default: true) - execute the first successful or all `jpeg` optimizers
* `optipng_options` (default: `array('-i0', '-o2', '-quiet')`) - an array of arguments to pass to the library
* `pngquant_options` (default: `array('--force')`)
* `pngcrush_options` (default: `array('-reduce', '-q', '-ow')`)
* `pngout_options` (default: `array('-s3', '-q', '-y')`)
* `advpng_options` (default: `array('-z', '-4', '-q')`)
* `gifsicle_options` (default: `array('-b', '-O5')`)
* `jpegoptim_options` (default: `array('--strip-all', '--all-progressive')`)
* `jpegtran_options` (default: `array('-optimize', '-progressive')`)
* `optipng_bin` (default: will be guessed) - you can enforce paths to binaries, but by default it will be guessed
* `pngquant_bin`
* `pngcrush_bin`
* `pngout_bin`
* `advpng_bin`
* `gifsicle_bin`
* `jpegoptim_bin`
* `jpegtran_bin`

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

You can obtain concrete optimizer by passing his name to `ImageOptimizer\OptimizerFactory`::`get` method:

```php
//default optimizer is `smart`
$optimizer = $factory->get();

//png optimizer
$pngOptimizer = $factory->get('png');

//jpegoptim optimizer etc.
$jpgOptimizer = $factory->get('jpegoptim');
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
