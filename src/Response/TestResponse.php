<?php

namespace Weble\JoomlaTestBench\Response;

class TestResponse
{
    protected $data;

    public function __construct(\stdClass $data)
    {
        $this->data = $data;
    }

    public function headers(): array
    {
        $headers = [
            'status' => 200,
        ];

        foreach ($this->data->headers ?? [] as $header) {
            $headers[$header['name']] = $header['value'];
        }

        return $headers;
    }

    public function status(): int
    {
        return (int) ($this->headers()['status'] ?? 200);
    }

    public function successful(): bool
    {
        return $this->status() >= 200 && $this->status() <= 299;
    }

    public function see(string $text): bool
    {
        return str_contains($this->body(), $text);
    }

    public function body(): string
    {
        return implode("", (array) $this->data->body ?? []);
    }

    public function cachable(): bool
    {
        return $this->data->cachable ?? false;
    }
}
