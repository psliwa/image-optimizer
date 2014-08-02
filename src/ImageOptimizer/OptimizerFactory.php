<?php


namespace ImageOptimizer;


use ImageOptimizer\Exception\Exception;
use Symfony\Component\Process\ExecutableFinder;

class OptimizerFactory
{
    const OPTIMIZER_SMART = 'smart';

    private $optimizers = array();
    private $options;
    private $executableFinder;

    public function __construct(array $options = array())
    {
        $this->executableFinder = new ExecutableFinder();

        $this->options = $options;
        $this->optimizers['optipng'] = new CommandOptimizer(
            new Command($this->executable('optipng'), array('-i0','-o2', '-quiet'))
        );
        $this->optimizers['pngquant'] = new CommandOptimizer(
            new Command($this->executable('pngquant'), array('--speed=1','--ext=.png', '--force'))
        );

        $this->optimizers['png'] = new ChainOptimizer(array(
            $this->optimizers['pngquant'],
            $this->optimizers['optipng'],
        ));
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
     * @throws Exception
     */
    public function getOptimizer($name = self::OPTIMIZER_SMART)
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