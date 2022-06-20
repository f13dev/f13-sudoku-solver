<?php
/*
Plugin Name: F13-sudoku-solver
Plugin URI: http://www.f13.dev
Description: Solve Sudoku puzzles
Version: 0.1
Author: Jim Valentine
Author URI: http://f13.dev
Text Domain: f13-sudoku-solver
*/

namespace F13\SudokuSolver;

if (!function_exists('get_plugins')) require_once(ABSPATH.'wp-admin/includes/plugin.php');
if (!defined('F13_SUDOKU_SOLVER')) define('F13_SUDOKU_SOLVER', get_plugin_data(__FILE__, false, false));
if (!defined('F13_SUDOKU_SOLVER_PATH')) define('F13_SUDOKU_SOLVER_PATH', plugin_dir_path( __FILE__ ));
if (!defined('F13_SUDOKU_SOLVER_URL')) define('F13_SUDOKU_SOLVER_URL', plugin_dir_url(__FILE__));

class Plugin
{
    public function init()
    {
        spl_autoload_register(__NAMESPACE__.'\Plugin::loader');
        add_action('wp_enqueue_scripts', array($this, 'load'));

        new Controllers\Control();
        
        if (defined('DOING_AJAX') && DOING_AJAX) {
            new Controllers\Ajax();
        }
    }

    public static function load()
    {
        wp_enqueue_script('f13-sudoku-solver', F13_SUDOKU_SOLVER_URL.'js/f13-sudoku-solver.js', array('jquery'), F13_SUDOKU_SOLVER['Version']);
        wp_enqueue_style('f13-sudoku-solver', F13_SUDOKU_SOLVER_URL.'css/f13-sudoku-solver.css', array(), F13_SUDOKU_SOLVER['Version']);
    }

    public static function loader($name)
    {
        $name = trim(ltrim($name, '\\'));
        if (strpos($name, __NAMESPACE__) !== 0) {
            return;
        }
        $file = str_replace(__NAMESPACE__, '', $name);
        $file = ltrim(str_replace('\\', DIRECTORY_SEPARATOR, $file), DIRECTORY_SEPARATOR);
        $file = plugin_dir_path(__FILE__).strtolower($file).'.php';

        if ($file !== realpath($file) || !file_exists($file)) {
            wp_die('Class not found: '.htmlentities($name));
        } else {
            require_once $file;
        }
    }
}

$p = new Plugin();
$p->init();