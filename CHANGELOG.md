CHANGELOG
=========

* 2.0.5 (2021-09-30)

  * [#77] Fixed the bug that the copied files were not deleted when the optimization failed

* 2.0.4 (2021-02-23)

  * [#75] Support for PHP8

* 2.0.2 (2020-01-11)

  * [#73] Support for Symfony 5
    
* 2.0.1 (2019-09-24)

  * [#70] Change log level from notice to error and change log message

* 2.0.0 (2019-07-13)

  * [#21] Add configurable timeout for single optimizer. Use
    symfony-process component as an implementation.
  * [#60] Skip larger files for pngquant optimizer
  * Adopt source code to more modern php versions (7.1+)
  * [#61] New option - allow to change output filepath
    (`output_filepath_pattern` option)

* 1.2.2 (2019-06-21)

  * [#58] Easy way to add custom optimizers.
  * [#57] Clear error message when "exec" function is not available.

* 1.2.1 (2018-12-17)

  * [#64] Bump psr/log version to 1.*

* 1.2.0 (2018-04-11)

  * [#46] Add support for svg files (using svgo library)

* 1.1.3 (2018-04-09)

  * [#53, #54, #50] Compatibility with Symfony 4

* 1.1.2 (2017-07-21)

  * [#44] Throw exception for Permissions Denied

* 1.1.1 (2017-06-28)

  * [#30] Fix "Command not found" issue related to open_basedir
  
* 1.1.0 (2017-03-25)

  * [#8] Chain optimizers' better behaviour:
    * execute only first successful optimizer by default (new options: `execute_only_first_png_optimizer` and `execute_only_first_jpeg_optimizer`)
    * ignore error when first optimizer fails, but second one succeeds
    * report an error when all optimizers fail (an error is ignored when `ignore_errors` is enabled)
    * BC break - `ChainOptimizer` constructor now requires 2nd parameter, and adds 3rd parameter logger:
```diff    
-    public function __construct(array $optimizers, $executeFirst = false)
+    public function __construct(array $optimizers, $executeFirst, LoggerInterface $logger)
```
