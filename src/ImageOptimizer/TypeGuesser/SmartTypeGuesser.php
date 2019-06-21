<?php
declare(strict_types=1);

namespace ImageOptimizer\TypeGuesser;


class SmartTypeGuesser implements TypeGuesser
{
    /**
     * @var TypeGuesser[]
     */
    private $typeGuessers;

    public function __construct()
    {
        try {
            $this->typeGuessers[] = new GdTypeGuesser();
        } catch (\RuntimeException $e) {
            // ignore, skip GdTypeGuesser
        }
        $this->typeGuessers[] = new ExtensionTypeGuesser();
    }

    public function guess(string $filepath): string
    {
        foreach($this->typeGuessers as $typeGuesser) {
            $type = $typeGuesser->guess($filepath);

            if($type !== self::TYPE_UNKNOWN) {
                return $type;
            }
        }

        return self::TYPE_UNKNOWN;
    }
}