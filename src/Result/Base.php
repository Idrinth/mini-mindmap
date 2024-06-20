<?php

namespace De\Idrinth\MiniMindmap\Result;

use De\Idrinth\MiniMindmap\Result;

abstract class Base implements Result
{
    protected array $headers = [];
    protected array $cookies = [];
    protected mixed $data;

    public function addHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    public function clearHeaders(): void
    {
        $this->headers = [];
    }

    public function setCookie(string $name, string $value, int $expiresIn = 86400): void
    {
        $this->cookies[$name] = [$value, $expiresIn];
    }

    public function clearCookies(): void
    {
        $this->cookies = [];
    }

    public function setContent(mixed $content): void
    {
        $this->data = $content;
    }
    protected function sendHeaders(): void
    {
        foreach ($this->headers as $header => $value) {
            header("$header: $value", true, 200);
        }
        foreach ($this->cookies as $name => $data) {
            setcookie($name, $data[0], $data[1], '/', $_SERVER['HTTP_HOST'], true, true);
        }
    }
}