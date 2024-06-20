<?php

namespace De\Idrinth\MiniMindmap;

interface Result
{
    function addHeader(string $name, string $value): void;
    function clearHeaders(): void;
    function setCookie(string $name, string $value, int $expiresIn = 86400): void;
    function clearCookies(): void;
    function setContent(mixed $content): void;
    function send(): void;
}