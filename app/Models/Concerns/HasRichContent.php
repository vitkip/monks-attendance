<?php

namespace App\Models\Concerns;

trait HasRichContent
{
    /**
     * Safe-to-render HTML for the `content` column.
     *
     * Content authored through the TipTap editor is already HTML (and was
     * purified before saving), but rows created before the editor existed
     * hold plain text with blank-line-separated paragraphs. Detect that case
     * and escape + wrap it into <p>/<br> so it displays and edit-populates
     * the same way. Everything is re-purified as a final safety net in case
     * a row was ever edited outside the app.
     */
    public function getContentHtmlAttribute(): string
    {
        $content = (string) $this->content;

        if ($content !== '' && strip_tags($content) === $content) {
            $content = collect(preg_split('/\n{2,}/', trim($content)))
                ->map(fn (string $paragraph) => '<p>' . nl2br(e(trim($paragraph))) . '</p>')
                ->implode('');
        }

        return clean($content, 'editor');
    }
}
