<?php
declare(strict_types=1);

namespace BenyCode\Slim\Validation\Encoder;

interface EncoderInterface
{
    public function encode(array $data): string;

    public function getContentType(): string;
}
