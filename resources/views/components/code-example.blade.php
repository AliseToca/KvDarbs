@props([
    'language' => null,
    'showCopy' => true,
])

@php
    $lines = explode("\n", $slot);

    // Normalize line endings
    $lines = str_replace(["\r\n", "\r"], "\n", $lines);

    $commonIndentLength = PHP_INT_MAX;

    for ($i = 1; $i < count($lines); $i++) {
        $line = $lines[$i];
        if (trim($line) !== '') {
            $indentLength = strlen($line) - strlen(ltrim($line));
            $commonIndentLength = min($commonIndentLength, $indentLength);
        }
    }

    $formattedCode = '';
    for ($i = 0; $i < count($lines); $i++) {
        $line = $i === 0 ? $lines[0] : substr($lines[$i], $commonIndentLength);
        if (preg_match('/^\s*<pre>\s*$/', $line) || preg_match('/^\s*<\/pre>\s*$/', $line)) {
            continue;
        }
        $formattedCode .= $line . "\n";
    }

    $codeClass = $language ? "language-{$language}" : 'hljs';
    $codeId = 'code_' . uniqid();
@endphp

<div class="code-example group relative">
    @if ($showCopy)
        <button
            class="button coppy"
            data-copy
            data-copy-target="{{ $codeId }}"
        >Copy</button>
    @endif
    <pre><code class="{{ $codeClass }}" id="{{ $codeId }}">{{ $formattedCode }}</code></pre>
</div>
