<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Json5\Engine\Helpers;

trait RenderCommentTrait
{
    /** @var resource */
    private readonly mixed $resource;

    private function renderComment(string|null $comment, string $indent): void
    {
        if ($comment === null) {
            return;
        }

        $lines = explode("\n", $comment);

        foreach ($lines as $line) {
            $this->renderCommentLine($line, $indent);
            fwrite($this->resource, "\n");
        }
    }

    private function renderInlineComment(string|null $comment, string $prefix, string $postfix): void
    {
        if ($comment === null) {
            return;
        }

        $comment = str_replace('*/', "*\u{200b}/", $comment);

        fwrite($this->resource, $prefix);
        fwrite($this->resource, '/* ');
        fwrite($this->resource, $comment);
        fwrite($this->resource, ' */');
        fwrite($this->resource, $postfix);
    }

    private function renderCommentLine(string|null $commentLine, string $indent): void
    {
        if ($commentLine === null) {
            return;
        }

        fwrite($this->resource, $indent);
        fwrite($this->resource, '//');
        if ($commentLine !== '') {
            fwrite($this->resource, ' ');
            fwrite($this->resource, $commentLine);
        }
    }
}
