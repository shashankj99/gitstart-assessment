<?php

namespace App\DTO\Query;

class CommonRequestQuery {
    public function __construct(
        public readonly int $page = 1,
        public readonly int $limit = 25,
        public readonly string $order = 'desc',
        public readonly string $search = '',
    ) {}
}
