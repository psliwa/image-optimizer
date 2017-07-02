CHANGELOG
=========

* 1.1.1 (2017-06-28)

  * [#30] Fix "Command not found" issue related to open_basedir
  
* 1.1.0 (2017-03-25)

  * [#8] Chain optimizers' better behaviour:
    * execute only first successful optimizer by default (new options: `execute_only_first_png_optimizer` and `execute_only_first_jpeg_optimizer`)
    * ignore error when first optimizer fails, but second one succeeds
    * report an error when all optimizers fail (an error is ignored when `ignore_errors` is enabled)
