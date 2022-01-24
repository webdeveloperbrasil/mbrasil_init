<?php
// Baseado: https://wisdmlabs.com/blog/create-settings-options-page-for-wordpress-plugin/

function mbrasil_init_register_settings()
{
    add_option('mbrasil_init_option_name', 'This is my option value.');
    register_setting('mbrasil_init_options_group', 'mbrasil_init_option_name', 'mbrasil_init_callback');
}
add_action('admin_init', 'mbrasil_init_register_settings');

function mbrasil_init_register_options_page()
{
    add_options_page('Plugin MBrasil INIT', 'MBINIT', 'manage_options', 'mbrasil_init', 'mbrasil_init_options_page');
}
add_action('admin_menu', 'mbrasil_init_register_options_page');

/**
 * Return first doc comment found in this file.
 *
 * @return string
 */
function getFileCommentBlock($file_name)
{
    if (!is_dir($file_name)) {
        $Comments = array_filter(
            token_get_all(file_get_contents($file_name)), function ($entry) {
                return $entry[0] == T_DOC_COMMENT;
            }
        );
        $fileComment = array_shift($Comments);
        if (isset($fileComment[1])) {
            return $fileComment[1];
        }
    }

}

if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle)
    {
        return empty($needle) || strpos($haystack, $needle) !== false;
    }
}

function mbrasil_init_options_page()
{

    $plugin_data = get_plugin_data(WP_PLUGIN_DIR . "/mbrasil_init/index.php");

    $plugin_dir = WP_PLUGIN_DIR . '/mbrasil-snippets/';
    $root_files = scandir($plugin_dir);

    // https://stackoverflow.com/questions/14680121/include-just-files-in-scandir-array
    /* $root_files = array_filter(scandir($plugin_dir), function ($file) {
    return !is_dir($plugin_dir . $file);
    }); */

    //print_a($root_files);
    /**
     * @plugin_dir - ../wp-content/plugins/mbrasil-snippets/
     * @root_files - array com nome de todos os arquivos
     * @dir_name - nome do subfolder ou
     */
    function getFilesOnFolder($isfile = true, $plugin_dir = '', $root_files = '', $dir_name = false)
    {

        $php_files = '';
        if (is_dir($plugin_dir)) {
            $php_files .= '<ol>';
            // Percorre array com nomes de arquivos (pode ser um folder)
            foreach ($root_files as $file) {
                $path_dir = $plugin_dir . $dir_name . $file;
                //print_a($path_dir) . '<hr>';

                if ($isfile) {
                    if (!is_dir($path_dir)) {
                        $comment = getFileCommentBlock($path_dir);
                        $start_comment = array("/**", "*/", " * ");
                        $txt = $comment != '' ? "<br><b>Comment:</b>" . str_replace($start_comment, "", $comment) : '<br><b style="color:red;">NOT Comment</b>';
                        $php_files .= "<li>$file $txt</li>";
                    }

                } else {
                    if (!str_contains($file, '.')) {
                        $php_files .= "<li>$file</li>";
                    }

                }
            }
            $php_files .= '</ol>';
        }
        return $php_files;
    };
    $php_root_files = getFilesOnFolder(true, $plugin_dir, $root_files, '/');

    /**
     * 1º NIVEL SUBFOLDERS
     */
    if (is_dir($plugin_dir)) {
        $php_root_folders = '<ol>';
        foreach ($root_files as $dir_name) {
            $path_dir = $plugin_dir . '/' . $dir_name;
            if (is_dir($path_dir) && !str_contains($path_dir, '.')) {

                $root_files = scandir($plugin_dir . '/' . $dir_name);

                $php_files = getFilesOnFolder(false, $plugin_dir, $root_files, $dir_name);

                $php_root_folders .= "<li>$dir_name $php_files </li>";
            }
        }
        $php_root_folders .= '</ol>';
    }
    ?>

<div>
    <h2>MBrasil INIT - Version: <?php echo $plugin_data['Version'] ?></h2>
    <hr>
    <h3>COMO USAR</h3>
    <p><strong>DIA 1:</strong> Seus scripts php devem ser salvos na pasta: <b>wp_content/plugins/mbrasil-snippets</b>.
        Nas primeiras linhas
        adicione um comentátio /** comentário */ descrevendo o propósito do script. Utilize como modelo o script:
        hello-world.php como exemplo.</p>
    <h4>Snippets root: mbrasil-snippets</h4>
    <?php echo $php_root_files; ?>
    <hr>
    <p><strong>DICA 2</strong>: Você pode criar até 2 níveis de subpastas.</p>
    <h4>Subpastas</h4>
    <?php echo $php_root_folders; ?>
</div>

<?php

}?>