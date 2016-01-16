<?php


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

    private $optimizers = array();
    private $options;
    private $executableFinder;
    private $logger;

    public function __construct(array $options = array(), LoggerInterface $logger = null)
    {
        $this->executableFinder = new ExecutableFinder();
        $this->logger = $logger ?: new NullLogger();

        $this->setOptions($options);
        $this->setUpOptimizers();
    }

    private function setOptions(array $options)
    {
        $this->options = $this->getOptionsResolver()->resolve($options);
    }

    protected function getOptionsResolver()
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(array(
            'ignore_errors' => true,
            'optipng_options' => array('-i0', '-o2', '-quiet'),
            'pngquant_options' => array('--force'),
            'pngcrush_options' => array('-reduce', '-q', '-ow'),
            'pngout_options' => array('-s3', '-q', '-y'),
            'gifsicle_options' => array('-b', '-O5'),
            'jpegoptim_options' => array('--strip-all', '--all-progressive'),
            'jpegtran_options' => array('-optimize', '-progressive'),
            'advpng_options' => array('-z', '-4', '-q')
        ));

        $method = is_callable(array($resolver, 'setDefined')) ? 'setDefined' : 'setOptional';

        $resolver->$method(array(
            'optipng_bin',
            'pngquant_bin',
            'pngcrush_bin',
            'pngout_bin',
            'gifsicle_bin',
            'jpegoptim_bin',
            'jpegtran_bin',
            'advpng_bin'
        ));

        return $resolver;
    }

    protected function setUpOptimizers()
    {
        $this->optimizers['optipng'] = $this->wrap(new CommandOptimizer(
            new Command($this->executable('optipng'), $this->options['optipng_options'])
        ));
        $this->optimizers['pngquant'] = $this->wrap(new CommandOptimizer(
            new Command($this->executable('pngquant'), $this->options['pngquant_options']),
            function($filepath){
                $ext = pathinfo($filepath, PATHINFO_EXTENSION);
                return array('--ext='.($ext ? '.'.$ext : ''), '--');
            }
        ));
        $this->optimizers['pngcrush'] = $this->wrap(new CommandOptimizer(
            new Command($this->executable('pngcrush'), $this->options['pngcrush_options'])
        ));
        $this->optimizers['pngout'] = $this->wrap(new CommandOptimizer(
            new Command($this->executable('pngout'), $this->options['pngout_options'])
        ));
        $this->optimizers['advpng'] = $this->wrap(new CommandOptimizer(
            new Command($this->executable('advpng'), $this->options['advpng_options'])
        ));
        $this->optimizers['png'] = new ChainOptimizer(array(
            $this->optimizers['pngquant'],
            $this->optimizers['optipng'],
            $this->optimizers['pngcrush'],
            $this->optimizers['advpng']
        ));

        $this->optimizers['gif'] = $this->optimizers['gifsicle'] = $this->wrap(new CommandOptimizer(
            new Command($this->executable('gifsicle'), $this->options['gifsicle_options'])
        ));

        $this->optimizers['jpegoptim'] = $this->wrap(new CommandOptimizer(
            new Command($this->executable('jpegoptim'), $this->options['jpegoptim_options'])
        ));
        $this->optimizers['jpegtran'] = $this->wrap(new CommandOptimizer(
            new Command($this->executable('jpegtran'), $this->options['jpegtran_options']),
            function ($filepath) {
                return array('-outfile', $filepath);
            }
        ));
        $this->optimizers['jpeg'] = $this->optimizers['jpg'] = new ChainOptimizer(array(
            $this->unwrap($this->optimizers['jpegtran']),
            $this->unwrap($this->optimizers['jpegoptim']),
        ), true);

        $this->optimizers[self::OPTIMIZER_SMART] = $this->wrap(new SmartOptimizer(array(
            TypeGuesser::TYPE_GIF => $this->optimizers['gif'],
            TypeGuesser::TYPE_PNG => $this->optimizers['png'],
            TypeGuesser::TYPE_JPEG => $this->optimizers['jpeg'],
        )));
    }

    private function wrap(Optimizer $optimizer)
    {
        return $this->option('ignore_errors', true) ? new SuppressErrorOptimizer($optimizer, $this->logger) : $optimizer;
    }

    private function unwrap(Optimizer $optimizer)
    {
        return $optimizer instanceof SuppressErrorOptimizer ? $optimizer->unwrap() : $optimizer;
    }

    private function executable($name)
    {
        $executableFinder = $this->executableFinder;
        return $this->option($name.'_bin', function() use($name, $executableFinder){
            return $executableFinder->find($name, $name);
        });
    }

    private function option($name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $this->resolveDefault($default);
    }

    /**
     * @param string $name
     * @return Optimizer
     * @throws Exception When requested optimizer does not exist
     */
    public function get($name = self::OPTIMIZER_SMART)
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
