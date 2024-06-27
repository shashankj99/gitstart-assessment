<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class RegisterUserDto
{
    public function __construct(
        #[NotBlank()]
        #[Email()]
        #[Type('string')]
        public readonly string $email,
        #[NotBlank()]
        #[Type('string')]
        public readonly string $password,
    ) {
    }
}
