# Image Optimizer

This library is handy and very easy to use optimizer for image files. It uses optipng, pngquant, jpegoptim and few more libraries,
so before use it you should install proper libraries on your server. To project is included Vagrantfile that defines testing
virtual machine with all libraries installed, so you can check Vagrantfile how to install all those stuff.

Thanks to ImageOptimizer and librares that it uses, your image files can be **10%-70% smaller**.

# Basic usage

```php

    $factory = new \ImageOptimizer\OptimizerFactory();
    $optimizer = $factory->get();
    
    $filepath = /* path to image */;
    
    $optimizer->optimize($filepath);
    //optimized file overwrites original one

```

# License

**MIT**
