<?php

namespace App\Contracts;

interface Rankable
{
    public function cover(): ?string;

    public function name(): string;

    public function spotifyId(): string;

    public function spotifyUrl(): string;
}
