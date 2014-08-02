<?php


namespace ImageOptimizer\TypeGuesser;


class SmartTypeGuesser implements TypeGuesser
{
    /**
     * @var TypeGuesser
     */
    private $typeGuesser;

    public function __construct()
    {
        try {
            $this->typeGuesser = new GdTypeGuesser();
        } catch (\RuntimeException $e) {
            $this->typeGuesser = new ExtensionTypeGuesser();
        }
    }

    public function guess($filepath)
    {
        return $this->typeGuesser->guess($filepath);
    }
}