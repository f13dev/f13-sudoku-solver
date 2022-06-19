<?php namespace F13\SudokuSolver\Views;

class View
{
    public $label_clear;
    public $label_load_demo;
    public $label_solve;

    public function __construct($params = array())
    {
        $this->label_clear = __('Clear', 'f13-sudoku-solver');
        $this->label_load_demo = __('Load demo', 'f13-sudoku-solver');
        $this->label_solve = __('Solve', 'f13-sudoku-solver');

        foreach ($params as $k => $v) {
            $this->{$k} = $v;
        }
    }

    /**
     * Wraps content in a container
     * 
     * @param String $c Input HTML
     * 
     * @return String Output HTML
     */
    public function _container($c)
    {
        $v = '<div style="position: relative">';
            $v .= '<div id="f13-sudoku-solver-container">';
                $v .= $c;
            $v .= '</div>';
        $v .= '</div>';

        return $v;
    }

    /**
     * Generate the solver HTML
     * 
     * @return String Output HTML
     */
    public function solver()
    {
        $v = '<form method="post" class="f13-sudoku-ajax" data-target="f13-sudoku-solver-container" data-url="'.admin_url('admin-ajax.php').'">';
            $v .= '<input type="hidden" name="submit" value="1">';
            $v .= '<input type="hidden" name="action" value="f13-sudoku-solver">';
            $v .= '<table class="f13-sudoku-solver-table" cellspacing="0">';
                for ($y = 1; $y <= 9; $y++) {
                    $v .= '<tr>';
                        for ($x = 1; $x <= 9; $x++) {
                            $class = '';
                            if ($y == 3 || $y == 6) {
                                $class .= 'border-bottom ';
                            } else 
                            if ($y == 4 || $y == 7) {
                                $class .= 'border-top ';
                            }
                            if ($x == 3 || $x == 6) {
                                $class .= 'border-right ';
                            } else 
                            if ($x == 4 || $x == 7) {
                                $class .= 'border-left ';
                            }

                            $orig = false; 
                            if ($this->solved) {
                                $val = $this->solved[$y][$x];
                                $orig = $this->solved[$y][$x] == $this->sudoku[$y][$x];
                                $class .= ($orig) ? 'original-value ' : '';
                            } else {
                                $val = (
                                    is_array($this->sudoku) &&
                                    array_key_exists($y, $this->sudoku) &&
                                    array_key_exists($x, $this->sudoku[$y]) &&
                                    $this->sudoku[$y][$x]
                                ) ? esc_attr($this->sudoku[$y][$x]) : '';
                            }
                            $name = 'sudoku['.$y.']['.$x.']';
                            
                            $v .= '<td class="'.$class.'">'; 
                                $v .= '<input type="text" min="0" max="9" name="'.$name.'" value="'.$val.'"  >';
                            $v .= '</td>';
                        }
                    $v .= '</tr>';
                }
            $v .= '</table>';
            $v .= $this->msg;
            $v .= '<input type="submit" value="'.$this->label_solve.'" class="f13-sudoku-solver-submit">';
        $v .= '</form>';

        $v .= '<form method="post" class="f13-sudoku-ajax f13-sudoku-solver-form-first-half" data-target="f13-sudoku-solver-container" data-url="'.admin_url('admin-ajax.php').'">';
            $v .= '<input type="hidden" name="action" value="f13-sudoku-solver">';
            $v .= '<input type="hidden" name="submit" value="0">';

            $key = array_rand($this->demo_puzzles); 

            foreach ($this->demo_puzzles[$key] as $y => $xd) {
                foreach ($xd as $x => $n) {
                    $name = 'sudoku['.$y.']['.$x.']';
                    $v .= '<input type="hidden" name="'.$name.'" value="'.$n.'">';
                }
            }

            $v .= '<input type="submit" value="'.$this->label_load_demo.'" class="f13-sudoku-solver-submit">';
        $v .= '</form>';

        $v .= '<form method="post" class="f13-sudoku-ajax f13-sudoku-solver-form-second-half" data-target="f13-sudoku-solver-container" data-url="'.admin_url('admin-ajax.php').'">';
            $v .= '<input type="hidden" name="action" value="f13-sudoku-solver">';
            $v .= '<input type="submit" value="'.$this->label_clear.'" class="f13-sudoku-solver-submit">';
        $v .= '</form>';
        
        return ($this->container) ? $this->_container($v) : $v;
    }
}  