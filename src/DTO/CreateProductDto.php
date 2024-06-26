<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class CreateProductDto
{
    public function __construct(
        #[NotBlank()]
        #[Type('string')]
        public readonly string $name,

        #[NotBlank()]
        #[Type('float')]
        public readonly float $price,

        #[NotBlank()]
        #[Type('int')]
        public readonly int $quantity,

        public readonly ?string $description,
    ) {}
}
