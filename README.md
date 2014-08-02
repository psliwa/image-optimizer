# Image Optimizer

This library is handy optimizer for image files. It uses optipng, pngquant, jpegoptim and few more libraries,
so before use it you should install proper libraries on your server. To project is included Vagrantfile, that defines testing
virtual machine with all libraries installed, so you can check Vagrantfile how to install all those stuff.

# Usage

```php

    $factory = new \ImageOptimizer\OptimizerFactory();
    $optimizer = $factory->get();
    
    $filepath = /* path to image */;
    
    $optimizer->optimize($filepath);
    //optimized file overwrites original one

```

# License

**MIT**
