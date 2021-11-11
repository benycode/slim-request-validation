<?php
declare(strict_types=1);

namespace BenyCode\Slim\Validation\Encoder;

use UnexpectedValueException;

final class JsonEncoder implements EncoderInterface
{
    public function encode(array $data): string
    {
        $result = \json_encode($data);

        if ($result === false) {
            throw new UnexpectedValueException(
                \sprintf('JSON encoding failed. Code: %s. Error: %s.', \json_last_error(), \json_last_error_msg())
            );
        }

        return $result;
    }

    public function getContentType(): string
    {
        return 'application/json';
    }
}
