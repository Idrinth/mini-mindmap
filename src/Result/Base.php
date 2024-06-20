<?php

namespace De\Idrinth\MiniMindmap\Result;

use De\Idrinth\MiniMindmap\Result;

abstract class Base implements Result
{
    protected array $headers = [];
    protected array $cookies = [];
    protected mixed $data;
    private int $statusCode = 200;
    protected function __construct(private $mime)
    {
    }

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
    public function setStatusCode(int $code): void
    {
        $this->statusCode = $code;
    }

    protected function sendHeaders(): void
    {
        foreach ($this->headers as $header => $value) {
            header("$header: $value", true, $this->statusCode);
        }
        header("Content-Type: {$this->mime}", true, $this->statusCode);
        foreach ($this->cookies as $name => $data) {
            setcookie($name, $data[0], $data[1], '/', $_SERVER['HTTP_HOST'], true, true);
        }
    }
}
