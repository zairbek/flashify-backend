<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Digest\Location\Domain\ValueObject;

use MarketPlace\Common\Domain\ValueObject\Translates\Translate;

class LocationName
{
    private string $name;
    /** @var Translate[] $translates */
    private array $translates;

    public function __construct(string $name, array $translates)
    {
        $this->name = $name;
        $this->translates = $translates;
    }

    public static function fromDB(string $name, ?array $translates = null): self
    {
        $translatesObj = collect($translates)->map(fn ($translate, $code) => new Translate($code, $translate))
            ->toArray();

        return new self($name, $translatesObj);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /** @return Translate[] */
    public function getTranslates(): array
    {
        return $this->translates;
    }

    public function getTranslate(string $lang): ?string
    {
        foreach ($this->translates as $translate) {
            if ($translate->getCode() === $lang) {
                return $translate->getValue();
            }
        }
        return null;
    }

    public function translatesToArray(): array
    {
        $bag = [];
        foreach ($this->translates as $translate) {
            $bag[$translate->getCode()] = $translate->getValue();
        }
        return $bag;
    }
}
