<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMNode;

class HtmlSanitizerService {
    /**
     * Allowed HTML tags for rich editorial articles and CMS pages.
     */
    protected static array $allowedArticleTags = [
        'p', 'br', 'hr', 'strong', 'em', 'b', 'i', 'u', 's', 'span', 'small',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'blockquote', 'cite', 'q',
        'ul', 'ol', 'li',
        'a', 'img', 'figure', 'figcaption',
        'pre', 'code', 'mark', 'kbd',
        'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td',
        'sub', 'sup', 'del', 'ins', 'abbr', 'details', 'summary',
        'div', 'section', 'article',
    ];

    /**
     * Allowed HTML tags for public user comments.
     */
    protected static array $allowedCommentTags = [
        'p', 'br', 'strong', 'em', 'b', 'i', 'u', 's', 'blockquote', 'code', 'pre', 'a'
    ];

    /**
     * Inherently dangerous tags that must be completely purged with their children.
     */
    protected static array $dangerousTags = [
        'script', 'style', 'iframe', 'frame', 'frameset', 'object',
        'embed', 'applet', 'meta', 'link', 'base', 'form', 'button',
        'input', 'textarea', 'select', 'option', 'optgroup', 'svg',
        'math', 'noscript', 'canvas', 'video', 'audio', 'source', 'track'
    ];

    /**
     * Whitelisted attributes per element tag.
     */
    protected static array $allowedAttributes = [
        '*'          => ['class', 'id', 'title', 'lang', 'dir'],
        'a'          => ['href', 'title', 'target', 'rel'],
        'img'        => ['src', 'alt', 'title', 'width', 'height', 'loading'],
        'table'      => ['border', 'cellpadding', 'cellspacing'],
        'th'         => ['colspan', 'rowspan', 'scope'],
        'td'         => ['colspan', 'rowspan', 'scope'],
        'blockquote' => ['cite'],
        'q'          => ['cite'],
        'code'       => ['data-language'],
        'pre'        => ['data-language'],
        'details'    => ['open'],
    ];

    /**
     * Sanitize rich article content from admin editor.
     */
    public static function cleanArticle(?string $html): string {
        return self::sanitize($html ?? '', self::$allowedArticleTags);
    }

    /**
     * Sanitize CMS static page content.
     */
    public static function cleanPage(?string $html): string {
        return self::sanitize($html ?? '', self::$allowedArticleTags);
    }

    /**
     * Sanitize public reader comments (strictly filtered).
     */
    public static function cleanComment(?string $html): string {
        return self::sanitize($html ?? '', self::$allowedCommentTags);
    }

    /**
     * AST tree-walker parser using DOMDocument with strict allowlists.
     */
    public static function sanitize(string $html, array $allowedTags): string {
        if (trim($html) === '') {
            return '';
        }

        $dom = new DOMDocument();
        $previousLibxmlUseErrors = libxml_use_internal_errors(true);

        // Wrap with xml encoding declaration and container to preserve UTF-8 and fragments
        $wrapped = '<?xml encoding="UTF-8"?><html><body><div id="_atelier_sanitizer_root">' . $html . '</div></body></html>';
        $dom->loadHTML($wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previousLibxmlUseErrors);

        $root = $dom->getElementById('_atelier_sanitizer_root');
        if (!$root) {
            return '';
        }

        self::traverseAndClean($root, $allowedTags);

        // Serialize sanitized child nodes
        $cleanHtml = '';
        foreach ($root->childNodes as $child) {
            $cleanHtml .= $dom->saveHTML($child);
        }

        return $cleanHtml;
    }

    /**
     * Recursively traverses and cleans every DOM node against security allowlists.
     */
    protected static function traverseAndClean(DOMNode $node, array $allowedTags): void {
        $children = [];
        foreach ($node->childNodes as $child) {
            $children[] = $child;
        }

        foreach ($children as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE) {
                /** @var DOMElement $child */
                $tagName = strtolower($child->tagName);

                // 1. Purge dangerous tags completely along with all descendant content
                if (in_array($tagName, self::$dangerousTags, true)) {
                    $node->removeChild($child);
                    continue;
                }

                // 2. If tag is not in allowed tags, unwrap children and remove tag
                if (!in_array($tagName, $allowedTags, true)) {
                    while ($child->firstChild) {
                        $node->insertBefore($child->firstChild, $child);
                    }
                    $node->removeChild($child);
                    continue;
                }

                // 3. Clean attributes on allowed tag
                self::cleanAttributes($child, $tagName);

                // 4. Recurse down children
                self::traverseAndClean($child, $allowedTags);
            }
        }
    }

    /**
     * Cleans element attributes against allowlists and protocol validators.
     */
    protected static function cleanAttributes(DOMElement $element, string $tagName): void {
        $allowed = array_merge(
            self::$allowedAttributes['*'] ?? [],
            self::$allowedAttributes[$tagName] ?? []
        );

        $attrsToRemove = [];

        foreach ($element->attributes as $attr) {
            $name = strtolower($attr->nodeName);
            $value = $attr->nodeValue;

            // Block all event handlers (onclick, onload, onerror, etc.)
            if (str_starts_with($name, 'on')) {
                $attrsToRemove[] = $name;
                continue;
            }

            // Block non-whitelisted attributes
            if (!in_array($name, $allowed, true)) {
                $attrsToRemove[] = $name;
                continue;
            }

            // Protocol validation for URI attributes
            if (in_array($name, ['href', 'src', 'cite'], true)) {
                $sanitizedUrl = self::sanitizeUri($value);
                if ($sanitizedUrl === null) {
                    $attrsToRemove[] = $name;
                } else {
                    $element->setAttribute($name, $sanitizedUrl);
                }
            }
        }

        foreach ($attrsToRemove as $attrName) {
            $element->removeAttribute($attrName);
        }

        // Force secure rel on external links
        if ($tagName === 'a' && $element->hasAttribute('target') && strtolower($element->getAttribute('target')) === '_blank') {
            $existingRel = strtolower($element->getAttribute('rel') ?: '');
            $relParts = array_filter(explode(' ', $existingRel));
            if (!in_array('noopener', $relParts, true)) $relParts[] = 'noopener';
            if (!in_array('noreferrer', $relParts, true)) $relParts[] = 'noreferrer';
            $element->setAttribute('rel', implode(' ', $relParts));
        }
    }

    /**
     * Validates and cleans a URI against dangerous pseudo-protocols.
     */
    protected static function sanitizeUri(string $uri): ?string {
        $trimmed = trim($uri);
        if ($trimmed === '' || $trimmed === '#') {
            return '#';
        }

        // Remove invisible characters and line breaks that bypass filters
        $normalized = preg_replace('/[\x00-\x1F\x7F\s]+/u', '', $trimmed);

        // Neutralize dangerous schemes (javascript:, data:, vbscript:, file:, etc.)
        if (preg_match('/^(javascript|vbscript|data|file):/i', $normalized)) {
            return null;
        }

        // Allow relative paths, anchors, and standard web protocols
        if (
            str_starts_with($trimmed, '/') ||
            str_starts_with($trimmed, '#') ||
            str_starts_with($trimmed, './') ||
            str_starts_with($trimmed, '../') ||
            preg_match('/^(https?|mailto|tel):/i', $trimmed)
        ) {
            return $trimmed;
        }

        // If scheme is unrecognized or relative alphanumeric without protocol
        if (!preg_match('/^[a-z0-9+.-]+:/i', $trimmed)) {
            return $trimmed;
        }

        return null;
    }
}
