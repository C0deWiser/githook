<?php

namespace Codewiser\Githook\Concerns;

use Symfony\Component\HttpFoundation\Request;

interface Carrier
{
    public function payload(Request $request): Payload;
}