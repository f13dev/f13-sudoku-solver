<?php namespace F13\SudokuSolver\Controllers;

class Ajax
{
    public function __construct()
    {
        add_action('wp_ajax_f13-sudoku-solver', array($this, 'sudoku'));
        add_action('wp_ajax_nopriv_f13-sudoku-solver', array($this, 'sudoku'));     
    }

    public function sudoku() { $c = new Control(); echo $c->solver(); die; }
}