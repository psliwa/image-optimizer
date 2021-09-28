<?php
declare(strict_types=1);

namespace ImageOptimizer;


use ImageOptimizer\Exception\Exception;
use ImageOptimizer\TypeGuesser\TypeGuesser;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Process\ExecutableFinder;

class OptimizerFactory
{
    const OPTIMIZER_SMART = 'smart';

    private $optimizers = [];
    private $options;
    private $executableFinder;
    private $logger;

    public function __construct(array $options = [], LoggerInterface $logger = null)
    {
        $this->executableFinder = new ExecutableFinder();
        $this->logger = $logger ?: new NullLogger();

        $this->setOptions($options);
        $this->setUpOptimizers();
    }

    private function setOptions(array $options): void
    {
        $this->options = $this->getOptionsResolver()->resolve($options);
    }

    protected function getOptionsResolver(): OptionsResolver
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'ignore_errors' => true,
            'execute_only_first_png_optimizer' => true,
            'execute_only_first_jpeg_optimizer' => true,
            'optipng_options' => ['-i0', '-o2', '-quiet'],
            'pngquant_options' => ['--force', '--skip-if-larger'],
            'pngcrush_options' => ['-reduce', '-q', '-ow'],
            'pngout_options' => ['-s3', '-q', '-y'],
            'gifsicle_options' => ['-b', '-O5'],
            'jpegoptim_options' => ['--strip-all', '--all-progressive'],
            'jpegtran_options' => ['-optimize', '-progressive'],
            'advpng_options' => ['-z', '-4', '-q'],
            'svgo_options' => ['--disable=cleanupIDs'],
            'custom_optimizers' => [],
            'single_optimizer_timeout_in_seconds' => 60,
            'output_filepath_pattern' => '%basename%/%filename%%ext%'
        ]);

        $resolver->setDefined([
            'optipng_bin',
            'pngquant_bin',
            'pngcrush_bin',
            'pngout_bin',
            'gifsicle_bin',
            'jpegoptim_bin',
            'jpegtran_bin',
            'advpng_bin',
            'svgo_bin',
            'custom_optimizers',
            'single_optimizer_timeout_in_seconds'
        ]);

        return $resolver;
    }

    protected function setUpOptimizers(): void
    {
        $this->optimizers['optipng'] = $this->wrap(
            $this->commandOptimizer('optipng', $this->options['optipng_options'])
        );
        $this->optimizers['pngquant'] = $this->wrap(
            $this->commandOptimizer('pngquant', $this->options['pngquant_options'],
                function($filepath){
                    $ext = pathinfo($filepath, PATHINFO_EXTENSION);
                    return ['--ext='.($ext ? '.'.$ext : ''), '--'];
                }
            )
        );
        $this->optimizers['pngcrush'] = $this->wrap(
            $this->commandOptimizer('pngcrush', $this->options['pngcrush_options'])
        );
        $this->optimizers['pngout'] = $this->wrap(
            $this->commandOptimizer('pngout', $this->options['pngout_options'])
        );
        $this->optimizers['advpng'] = $this->wrap(
            $this->commandOptimizer('advpng', $this->options['advpng_options'])
        );
        $this->optimizers['png'] = $this->wrap(new ChainOptimizer([
            $this->unwrap($this->optimizers['pngquant']),
            $this->unwrap($this->optimizers['optipng']),
            $this->unwrap($this->optimizers['pngcrush']),
            $this->unwrap($this->optimizers['advpng'])
        ], $this->options['execute_only_first_png_optimizer'], $this->logger));

        $this->optimizers['gif'] = $this->optimizers['gifsicle'] = $this->wrap(
            $this->commandOptimizer('gifsicle', $this->options['gifsicle_options'])
        );

        $this->optimizers['jpegoptim'] = $this->wrap(
            $this->commandOptimizer('jpegoptim', $this->options['jpegoptim_options'])
        );
        $this->optimizers['jpegtran'] = $this->wrap(
            $this->commandOptimizer('jpegtran', $this->options['jpegtran_options'],
                function ($filepath) {
                    return ['-outfile', $filepath];
                }
            )
        );
        $this->optimizers['jpeg'] = $this->optimizers['jpg'] = $this->wrap(new ChainOptimizer([
            $this->unwrap($this->optimizers['jpegtran']),
            $this->unwrap($this->optimizers['jpegoptim']),
        ], $this->options['execute_only_first_jpeg_optimizer'], $this->logger));

        $this->optimizers['svg'] = $this->optimizers['svgo'] = $this->wrap(
            $this->commandOptimizer('svgo', $this->options['svgo_options'],
                function ($filepath) {
                    return ['--input' => $filepath, '--output' => $filepath];
                }
            )
        );

        foreach($this->options['custom_optimizers'] as $key => $options) {
            $this->optimizers[$key] = $this->wrap(
                $this->commandOptimizer($options['command'], isset($options['args']) ? $options['args'] : [])
            );
        }

        $this->optimizers[self::OPTIMIZER_SMART] = $this->wrap(new SmartOptimizer([
            TypeGuesser::TYPE_GIF => $this->unwrap($this->optimizers['gif']),
            TypeGuesser::TYPE_PNG => $this->unwrap($this->optimizers['png']),
            TypeGuesser::TYPE_JPEG => $this->unwrap($this->optimizers['jpeg']),
            TypeGuesser::TYPE_SVG => $this->unwrap($this->optimizers['svg'])
        ]));
    }

    private function commandOptimizer(string $command, array $args, $extraArgs = null): CommandOptimizer
    {
        return new CommandOptimizer(
            new Command($this->executable($command), $args, $this->options['single_optimizer_timeout_in_seconds']),
            $extraArgs
        );
    }

    private function wrap(Optimizer $optimizer): Optimizer
    {
        $optimizer = $optimizer instanceof ChangedOutputOptimizer ? $optimizer : new ChangedOutputOptimizer($this->option('output_filepath_pattern'), $optimizer);
        return $this->option('ignore_errors', true) ? new SuppressErrorOptimizer($optimizer, $this->logger) : $optimizer;
    }

    private function unwrap(Optimizer $optimizer): Optimizer
    {
        return $optimizer instanceof WrapperOptimizer ? $optimizer->unwrap() : $optimizer;
    }

    private function executable(string $name): string
    {
        $executableFinder = $this->executableFinder;
        return $this->option($name.'_bin', function() use($name, $executableFinder){
            return $executableFinder->find($name, $name);
        });
    }

    private function option(string $name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $this->resolveDefault($default);
    }

    /**
     * @param string $name
     * @return Optimizer
     * @throws Exception When requested optimizer does not exist
     */
    public function get(string $name = self::OPTIMIZER_SMART): Optimizer
    {
        if(!isset($this->optimizers[$name])) {
            throw new Exception(sprintf('Optimizer "%s" not found', $name));
        }

        return $this->optimizers[$name];
    }

    private function resolveDefault($default)
    {
        return is_callable($default) ? call_user_func($default) : $default;
    }
}
