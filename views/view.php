<?php namespace F13\SudokuSolver\Views;

use function PHPSTORM_META\map;

class View
{
    public function __construct($params = array())
    {
        foreach ($params as $k => $v) {
            $this->{$k} = $v;
        }
    }

    public function _container($c)
    {
        $v = '<div style="position: relative">';
            $v .= '<div id="f13-sudoku-solver-container" style="width: 344px; margin: auto;">';
                $v .= $c;
            $v .= '</div>';
        $v .= '</div>';

        return $v;
    }

    public function solver()
    {
        $v = '<form method="post" class="f13-sudoku-ajax" data-target="f13-sudoku-solver-container" data-url="'.admin_url('admin-ajax.php').'">';
            $v .= '<input type="hidden" name="submit" value="1">';
            $v .= '<input type="hidden" name="action" value="f13-sudoku-solver">';
            $v .= '<table class="f13-sudoku-solver-table" cellspacing="0" style="border: 1px solid #ccc;">';
                for ($y = 1; $y <= 9; $y++) {
                    $v .= '<tr style="padding: 0 !important">';
                        for ($x = 1; $x <= 9; $x++) {
                            $style = '';
                            if ($y == 3 || $y == 6) {
                                $style .= 'border-bottom: 1px solid #222; ';
                            } else 
                            if ($y == 4 || $y == 7) {
                                $style .= 'border-top: 1px solid #222; ';
                            }
                            if ($x == 3 || $x == 6) {
                                $style .= 'border-right: 1px solid #222; ';
                            } else 
                            if ($x == 4 || $x == 7) {
                                $style .= 'border-left: 1px solid #222; ';
                            }

                            $orig = false; 
                            if ($this->solved) {
                                $val = $this->solved[$y][$x];
                                $orig = $this->solved[$y][$x] == $this->sudoku[$y][$x];
                            } else {
                                $val = (
                                    is_array($this->sudoku) &&
                                    array_key_exists($y, $this->sudoku) &&
                                    array_key_exists($x, $this->sudoku[$y]) &&
                                    $this->sudoku[$y][$x]
                                ) ? esc_attr($this->sudoku[$y][$x]) : '';
                            }
                            $name = 'sudoku['.$y.']['.$x.']';
                            
                            $v .= '<td style="border:1px solid #ddd; padding: 0 !important; height: 30px; width: 30px; '.$style.' background: '.($orig && $val ? '#ccffcc;' : '#fff').'">'; 
                                $v .= '<input type="text" min="0" max="9" name="'.$name.'" style="width:30px; height: 30px; border: 0; font-size: 24px; background: transparent;" value="'.$val.'"  >';
                            $v .= '</td>';
                        }
                    $v .= '</tr>';
                }
            $v .= '</table>';
            $v .= $this->msg;
            $v .= '<input type="submit" value="Solve" class="f13-sudoku-solver-submit">';
        $v .= '</form>';

        $v .= '<form method="post" class="f13-sudoku-ajax" data-target="f13-sudoku-solver-container" data-url="'.admin_url('admin-ajax.php').'" style="width: calc(50% - 5px); display: inline-block; margin-right: 10px;">';
            $v .= '<input type="hidden" name="action" value="f13-sudoku-solver">';
            $v .= '<input type="hidden" name="submit" value="0">';
            $arr = array(
                0=>array(
                    1=>array(1=>2, 3=>6, 9=>7),
                    2=>array(1=>5, 2=>7, 4=>6, 6=>9),
                    3=>array(5=>2, 7=>1),
                    4=>array(1=>4, 2=>1, 3=>5, 5=>7, 6=>6, 8=>2, 9=>3),
                    5=>array(2=>9, 8=>8, 9=>1),
                    6=>array(1=>6, 3=>8, 4=>1, 5=>9, 6=>3, 8=>7, 9=>4),
                    7=>array(3=>4, 4=>2, 6=>1, 7=>7),
                    8=>array(5=>6, 6=>8, 7=>4, 9=>9),
                    9=>array(2=>5, 9=>6),
                ),
                1=>array(
                    1=>array(1=>5, 2=>3, 5=>7),
                    2=>array(1=>6, 4=>1, 5=>9, 6=>5),
                    3=>array(2=>9, 3=>8, 8=>6),
                    4=>array(1=>8, 5=>6, 9=>3),
                    5=>array(1=>4, 4=>8, 6=>3, 9=>1),
                    6=>array(1=>7, 5=>2, 9=>6),
                    7=>array(2=>6, 7=>2, 8=>8),
                    8=>array(4=>4, 5=>1, 6=>9, 9=>5),
                    9=>array(5=>8, 8=>7, 9=>9),
                ),
                2=>array(
                    1=>array(3=>2, 4=>7, 5=>8, 9=>3),
                    2=>array(6=>9, 7=>8, 9=>1),
                    3=>array(1=>4, 6=>3, 8=>7),
                    4=>array(1=>9, 3=>5, 6=>8),
                    5=>array(5=>7),
                    6=>array(4=>5, 7=>4, 9=>8),
                    7=>array(2=>6, 4=>4, 9=>7),
                    8=>array(1=>3, 3=>9, 4=>8),
                    9=>array(1=>8, 5=>3, 6=>1, 7=>6),
                ),
                3=>array(
                    1=>array(1=>3, 4=>8, 6=>1, 9=>2),
                    2=>array(1=>2, 3=>1, 5=>3, 7=>6, 9=>4),
                    3=>array(4=>2, 6=>4),
                    4=>array(1=>8, 3=>9, 7=>1, 9=>6),
                    5=>array(2=>6, 8=>5),
                    6=>array(1=>7, 3=>2, 7=>4, 9=>9),
                    7=>array(4=>5, 6=>9),
                    8=>array(1=>9, 3=>4, 5=>8, 7=>7, 9=>5),
                    9=>array(1=>6, 4=>1, 6=>7, 9=>3),
                ),
                4=>array(
                    1=>array(4=>7, 5=>9, 8=>3, 9=>4),
                    2=>array(1=>5, 3=>9, 4=>2, 8=>1, 9=>8),
                    3=>array(2=>3, 4=>6),
                    4=>array(1=>2, 2=>4, 4=>1),
                    5=>array(3=>8, 5=>4, 7=>9),
                    6=>array(6=>6, 8=>4, 9=>7),
                    7=>array(6=>8, 8=>2),
                    8=>array(1=>1, 2=>8, 6=>2, 7=>4, 9=>3),
                    9=>array(1=>4, 2=>7, 5=>1, 6=>3)
                )
            );

            $key = array_rand($arr); 

            foreach ($arr[$key] as $y => $xd) {
                foreach ($xd as $x => $n) {
                    $name = 'sudoku['.$y.']['.$x.']';
                    $v .= '<input type="hidden" name="'.$name.'" value="'.$n.'">';
                }
            }

            $v .= '<input type="submit" value="Load demo" class="f13-sudoku-solver-submit">';
        $v .= '</form>';

        $v .= '<form method="post" class="f13-sudoku-ajax" data-target="f13-sudoku-solver-container" data-url="'.admin_url('admin-ajax.php').'" style="width: calc(50% - 5px); display: inline-block;">';
            $v .= '<input type="hidden" name="action" value="f13-sudoku-solver">';
            $v .= '<input type="submit" value="Clear" class="f13-sudoku-solver-submit">';
        $v .= '</form>';
        
        return ($this->container) ? $this->_container($v) : $v;
    }
}  