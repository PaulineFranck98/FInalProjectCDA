<?php

namespace App\Service;

interface LocationSearchServiceInterface
{
    public function search(array $filters = []): array;
}