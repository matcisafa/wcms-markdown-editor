(function() {
    document.addEventListener('DOMContentLoaded', function() {
        let editors = {};
        let timeoutSave = null;

        // MathJax konfigürasyonu
        window.MathJax = {
            tex2jax: {
                inlineMath: [['$','$'], ['\\(','\\)']],
                displayMath: [['$$','$$'], ['\\[','\\]']],
                processEscapes: true
            }
        };

        // Her markdown-editor class'ına sahip element için CodeMirror editörü oluştur
        document.querySelectorAll('.markdown-editor').forEach(function(element) {
            let editor = CodeMirror.fromTextArea(element, {
                mode: 'markdown',
                lineNumbers: true,
                lineWrapping: true,
                theme: 'default',
                extraKeys: {"Enter": "newlineAndIndentContinueMarkdownList"}
            });

            // Editör değişikliklerini takip et
            editor.on('change', function() {
                clearTimeout(timeoutSave);
                let content = editor.getValue();
                timeoutSave = setTimeout(function() {
                    saveContent(element.id, content);
                }, 1000);

                if (typeof MathJax !== 'undefined') {
                    MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
                }
            });

            editors[element.id] = editor;
        });

        // İçeriği kaydet
        function saveContent(id, content) {
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'fieldname': id,
                    'content': content,
                    'target': 'pages',
                    'token': window.token
                })
            })
            .then(response => {
                if (response.ok) {
                    let save = document.getElementById('save');
                    if (save) {
                        save.style.display = 'block';
                        setTimeout(() => save.style.display = 'none', 1000);
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });
})();
