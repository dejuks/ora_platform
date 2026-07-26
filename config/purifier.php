<?php

/**
 * Config for mews/purifier (wraps HTMLPurifier). CKEditor runs in the
 * browser, but the /wiki/manage/articles/{article} update endpoint
 * still accepts raw POST data — someone could skip the editor
 * entirely and post a <script> tag directly. This is what actually
 * stops that: every save is sanitized server-side to only the tags
 * the editor's toolbar can produce, regardless of what was posted.
 *
 * Only defines the "wiki_article" profile used by
 * App\Http\Controllers\Wiki\ArticleController — other modules aren't
 * affected. If the toolbar in _content-editor.blade.php changes,
 * update HTML.Allowed to match.
 */
return [

    'encoding' => 'UTF-8',
    'finalize' => true,
    'ignoreNonStrings' => false,
    'cachePath' => storage_path('app/purifier'),
    'cacheFileMode' => 0755,

    'settings' => [

        'default' => [
            'HTML.Doctype' => 'HTML 4.01 Transitional',
            'HTML.Allowed' => 'p,br,strong,b,em,i,u,a[href|title],ul,ol,li,blockquote,h2,h3,h4,table,thead,tbody,tr,td,th,code,pre',
            'CSS.AllowedProperties' => '',
            'AutoFormat.AutoParagraph' => false,
            'AutoFormat.RemoveEmpty' => true,
        ],

        // Matches the CKEditor toolbar in
        // resources/views/modules/wiki/articles/_content-editor.blade.php
        'wiki_article' => [
            'HTML.Doctype' => 'HTML 4.01 Transitional',
            'HTML.Allowed' => 'p,br,strong,em,u,a[href|title],ul,ol,li,blockquote,h2,h3,table,thead,tbody,tr,td,th',
            'CSS.AllowedProperties' => '',
            'AutoFormat.AutoParagraph' => false,
            'AutoFormat.RemoveEmpty' => true,
            'HTML.TargetBlank' => true,
        ],

    ],

];
