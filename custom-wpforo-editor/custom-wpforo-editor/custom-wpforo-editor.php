<?php
/*
 * Plugin Name: <a href="https://mrlab.altervista.org/community/wpforo-plugin/custom-wpforo-editor-plugin/" target="_blank">Custom WPForo Editor</a>
 * Description: This plugin adds new features to the wpforo editor. In addition to new buttons, an additional menu bar is also added, making the editor more elegant, tidy and more user friendly.
 * Author: <a href="https://mrlab.altervista.org/community" target="_blank">MRLab Community</a>
 * Version: 1.0
 * Text Domain: Custom-WPForo-Editor
 */

// Assicurati che il plugin non possa essere richiamato direttamente
if (!defined('ABSPATH')) {
    exit;
}

// Rimuovi i plugin predefiniti di TinyMCE (se esistono)
add_filter('tiny_mce_plugins', function ($plugins) {
    // Rimuovi i plugin predefiniti che causano conflitti
    $plugins_to_remove = ['codesample', 'wordcount'];
    return array_diff($plugins, $plugins_to_remove);
});

// Aggiungi il filtro per modificare le impostazioni dell'editor WPForo
add_filter('wpforo_editor_settings', function ($settings) {
    // Inizializza l'array `tinymce` se non esiste
    if (empty($settings['tinymce']) || !is_array($settings['tinymce'])) {
        $settings['tinymce'] = [];
    }

    // Imposta i plugin TinyMCE SOLO quelli che vogliamo caricare
    $settings['plugins'] = 'compat3x,fullscreen,hr,paste,textcolor,lists,table,visualblocks,visualchars,insertdatetime,charmap,searchreplace,anchor,advlist,codesample,wordcount,code';

    // Configura la menubar
    $settings['tinymce']['menubar'] = 'edit insert view format table tools';

    // Configura la toolbar1
    $settings['tinymce']['toolbar1'] = 'fontsizeselect,fontselect,bold,italic,underline,strikethrough,forecolor,backcolor,alignleft,aligncenter,alignright,alignjustify,bullist,numlist,hr,subscript,superscript,outdent,indent,link,unlink,blockquote,wpf_spoil,undo,redo,pastetext,source_code,fullscreencut,removeformat,searchreplace,anchor,codesample,wordcount';

    // Forza l'uso del plugin wordcount personalizzato
    $settings['tinymce']['external_plugins'] = [
        'wordcount' => plugins_url('tinymce-plugins/wordcount/plugin.min.js', __FILE__),
    ];

    return $settings;
});

// Registra i plugin TinyMCE aggiuntivi
add_action('init', function () {
    // Percorso alla cartella dei plugin TinyMCE nel plugin
    $plugin_dir = plugin_dir_path(__FILE__) . 'tinymce-plugins/';

    // Elenco dei plugin da caricare
    $plugins = [
        'compat3x',
        'fullscreen',
        'hr',
        'paste',
        'textcolor',
        'lists',
        'table',
        'visualblocks',
        'visualchars',
        'insertdatetime',
        'charmap',
        'searchreplace',
        'anchor',
        'advlist',
        'codesample',
        'wordcount',
        'code' // Plugin problematico
    ];

    // Aggiungi ogni plugin al percorso di TinyMCE
    foreach ($plugins as $plugin) {
        $path = $plugin_dir . $plugin . '/plugin.min.js';
        if (file_exists($path)) {
            add_filter('mce_external_plugins', function ($external_plugins) use ($plugin, $path) {
                // Registra il plugin con il percorso corretto
                $external_plugins[$plugin] = plugins_url("tinymce-plugins/{$plugin}/plugin.min.js", __FILE__);
                return $external_plugins;
            });
        } else {
            // Debugging: Stampa un messaggio se il file non esiste
            error_log("Il file plugin.min.js per il plugin TinyMCE '{$plugin}' non esiste in: {$path}");
        }
    }
});

// Aggiungi uno script JavaScript e CSS per modificare l'estetica dell'editor
add_action('admin_footer', function () {
    ?>
    <style>
        /* Sovrascrivi il colore di sfondo della menubar */
        div.mce-menubar {
            background-color: #f6f7f7 !important;
            border-color: #dcdcde !important;
        }

        /* Aggiungi spaziatura ai pulsanti della toolbar */
        .mce-toolbar .mce-btn {
            margin: 5px !important;
        }

        /* Aggiungi spaziatura ai pulsanti personalizzati */
        .custom-toolbar button {
            margin: 5px !important;
            padding: 8px 12px !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Verifica se siamo nella pagina corretta (ad esempio, la pagina del forum)
            if (!document.querySelector('.wpforo-post-form')) {
                console.warn('Pagina non rilevante per l\'editor WPForo. Script ignorato.');
                return;
            }

            // Verifica se l'editor WPForo esiste
            const editorToolbar = document.querySelector('.wpforo-post-form .mce-toolbar');
            if (!editorToolbar) {
                console.error('Toolbar di WPForo non trovata.');
                return;
            }

            // Crea un contenitore per i pulsanti personalizzati
            const customToolbar = document.createElement('div');
            customToolbar.className = 'custom-toolbar'; // Aggiungi una classe per lo stile
            customToolbar.style.display = 'flex';
            customToolbar.style.gap = '5px';
            customToolbar.style.marginTop = '5px';

            // Pulsante "Stili"
            const stylesButton = document.createElement('button');
            stylesButton.textContent = 'Stili';
            stylesButton.onclick = function () {
                alert('Applica uno stile:\n1. Titolo 1\n2. Titolo 2\n3. Paragrafo');
                // Qui puoi aggiungere la logica per applicare gli stili
            };
            customToolbar.appendChild(stylesButton);

            // Pulsante "Azioni"
            const actionsButton = document.createElement('button');
            actionsButton.textContent = 'Azioni';
            actionsButton.onclick = function () {
                const currentDate = new Date().toLocaleDateString();
                const currentTime = new Date().toLocaleTimeString();
                alert(`Data: ${currentDate}\nOra: ${currentTime}`);
                // Qui puoi aggiungere la logica per inserire la data/ora
            };
            customToolbar.appendChild(actionsButton);

            // Aggiungi la toolbar personalizzata sotto la toolbar principale
            editorToolbar.parentNode.insertBefore(customToolbar, editorToolbar.nextSibling);
        });
    </script>
    <?php
});