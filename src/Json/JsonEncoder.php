<?php

declare(strict_types=1);

namespace Medas\HttpClient\Json;

use Medas\Core\Attributes\Service;

#[Service]
readonly class JsonEncoder
{
    public function __construct(
        private StringProtector $stringProtector,
    )
    {
    }

    public function encode(mixed $data): string
    {
        if (is_array($data)) {
            array_walk_recursive($data, function (&$value) {
                $value = $this->stringProtector->encode($value);
            });
        }
        else {
            $data = $this->stringProtector->encode($data);
        }

        return json_encode($data, flags: JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }

    public function decode(string $string): mixed
    {
        $data = json_decode($string, associative: true, flags: JSON_THROW_ON_ERROR);

        if (is_array($data)) {
            array_walk_recursive($data, function (&$value) {
                $value = $this->stringProtector->decode($value);
            });
        }
        else {
            $data = $this->stringProtector->decode($data);
        }

        return $data;
    }
}
