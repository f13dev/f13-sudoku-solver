<?php namespace F13\SudokuSolver\Controllers;

class Control
{
    public $request_method;

    public function __construct()
    {
        $this->request_method = ($_SERVER['REQUEST_METHOD'] === 'POST' ? INPUT_POST : INPUT_GET);
        add_shortcode('sudoku-solver', array($this, 'solver'));
    }

    public function _msg($msg, $mode)
    {
        switch ($mode) {
            case 'fully_solved': $style = 'background: #ccffcc;'; break;
            case 'partially_solved': $style = 'background: #FFBD33;'; break;
            default: $style = 'background: #ffcccc;';
        }
        $v = '<div style="padding: 5px; margin: 10px 0px 0px; text-align: center; border: 2px solid #ccc; '.$style.'">';
            $v .= $msg;
        $v .= '</div>';

        return $v;
    }

    public function solver() 
    {
        $submit = (int) filter_input($this->request_method, 'submit');

        $msg = '';
        $container = true;
        if (defined('DOING_AJAX') && DOING_AJAX) {
            $container = false;
        }

        $sudoku = filter_input($this->request_method, 'sudoku', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $solved = array();

        if ($submit) {
            $m = new \F13\SudokuSolver\Models\Model($sudoku);
            if ($m->validate_puzzle()) {
                $resp = $m->solve();
                $solved = $resp['resp'];
                if ($resp['mode'] == 'fully_solved') {
                    $msg = $this->_msg(
                        sprintf(__('Puzzle solved in %d milliseconds.'), $resp['time']),
                        $resp['mode'], 
                    );
                } else 
                if ($resp['mode'] == 'partially_solved') {
                    $msg = $this->_msg(
                        sprintf(__('Puzzle partially solved in %d milliseconds.'), $resp['time']),
                        $resp['mode'], 
                    );
                } else {
                    $msg = $this->_msg(
                        sprintf(__('Puzzle could not be solved - taking %d milliseconds'), $resp['time']),
                        $resp['mode'],
                    );
                }
            } else {
                $msg = $this->_msg(
                    __('This puzzle is not valid, please check you have entered the numbers correctly'),
                    'unsolved',
                );
            }
        }

        $v = new \F13\SudokuSolver\Views\View(array(
            'msg' => $msg,
            'container' => $container,
            'sudoku' => $sudoku,
            'solved' => $solved,
        ));

        return $v->solver();
    }
}