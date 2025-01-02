<?php
global $Wcms;

if (!defined('VERSION')) {
    die('Direct access is not allowed.');
}

class MarkdownEditor {
    private $Wcms;

    public function __construct() {
        global $Wcms;
        $this->Wcms = $Wcms;

        $this->Wcms->addListener('css', 'loadMarkdownEditorCSS');
        $this->Wcms->addListener('js', 'loadMarkdownEditorJS');
        $this->Wcms->addListener('editable', 'initializeMarkdownEditor');
    }
}

function loadMarkdownEditorJS($args) {
    global $Wcms;
    if ($Wcms->loggedIn) {
        $script = <<<EOT
        <script type="text/javascript">
        window.MathJax = {
            tex2jax: {
                inlineMath: [['$','$'], ['\\\\(','\\\\)']],
                displayMath: [['$$','$$'], ['\\\\[','\\\\]']],
                processEscapes: true
            },
            startup: {
                ready: () => {
                    MathJax.startup.defaultReady();
                }
            }
        };
        </script>
        <script type="text/javascript" id="MathJax-script" async
          src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js">
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/markdown/markdown.min.js"></script>
EOT;
        $script .= '<script>' . file_get_contents(__DIR__ . '/js/editor.js') . '</script>';
        $args[0] .= $script;
    }
    return $args;
}

function loadMarkdownEditorCSS($args) {
    global $Wcms;
    if ($Wcms->loggedIn) {
        $css = <<<EOT
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
EOT;
        $css .= '<style>' . file_get_contents(__DIR__ . '/css/editor.css') . '</style>';
        $args[0] .= $css;
    }
    return $args;
}

function initializeMarkdownEditor($contents) {
    global $Wcms;
    if ($Wcms->loggedIn) {
        foreach ($contents as &$content) {
            if (strpos($content, 'class="editable"') !== false) {
                $content = str_replace('class="editable"', 'class="markdown-editor"', $content);
            }
        }
    }
    return $contents;
}

if (!isset($Wcms->markdown_editor)) {
    $Wcms->markdown_editor = new MarkdownEditor();
}
