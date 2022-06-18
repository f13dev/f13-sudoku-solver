<?php namespace F13\SudokuSolver\Models ;

class Model
{
    public $x;  // x axis
    public $y;  // y axis
    public $c;  // cubes
    public $p;  // puzzle
    public $s;  // solved

    public function __construct($sudoku)
    {
        $this->p = $sudoku;
        $this->s = $sudoku;
        $this->_set_y();
        $this->_set_x();
        $this->_set_c();
    }

    public function validate_puzzle()
    {
        // Check that each number appears only once in rows
        foreach ($this->y as $row) {
            for ($n = 1; $n <= 9; $n++) {
                $count = 0;
                foreach ($row as $cell) {
                    if ($cell == $n) {
                        $count++;
                        if ($count > 1) {
                            return false;
                        }
                    }
                }
            }
        }

        // Check that each number appears only once in cols
        foreach ($this->x as $col) {
            for ($n = 1; $n <= 9; $n++) {
                $count = 0;
                foreach ($col as $cell) {
                    if ($cell == $n) {
                        $count++;
                        if ($count > 1) {
                            return false;
                        }
                    }
                }
            }
        }

        // Check that each number appears only once in cubes
        foreach ($this->c as $square) {
            for ($n = 1; $n <= 9; $n++) {
                $count = 0;
                foreach ($square as $cell) {
                    if ($cell == $n) {
                        $count++;
                        if ($count > 1) {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }

    public function _get_yx_from_c($cube, $cell)
    {
        switch ($cube) {
            case 1: case 4: case 7: $xs = array(1,2,3); break;
            case 2: case 5: case 8: $xs = array(4,5,6); break;
            case 3: case 6: case 9: $xs = array(7,8,9); break;
        }
        switch ($cube) {
            case 1: case 2: case 3: $yx = array(1,2,3); break;
            case 4: case 5: case 6: $ys = array(4,5,6); break;
            case 7: case 8: case 9: $ys = array(7,8,9); break;
        }

        switch ($cell) {
            case 1: case 2: case 3: $x = $xs[0]; break;
            case 4: case 5: case 6: $x = $xs[1]; break;
            case 7: case 8: case 9: $x = $xs[2]; break;
        }

        switch ($cell) {
            case 1: case 4: case 7: $y = $ys[0]; break;
            case 2: case 5: case 8: $y = $ys[1]; break;
            case 3: case 6: case 8: $y = $ys[2]; break;
        }

        return array(
            'x' => $x,
            'y' => $y,
        );
    }

    public function _get_c_id($y, $x)
    {
        switch ($y) {
            case 1: case 2: case 3: $cy = 1; $cys = array(1, 2, 3); break;
            case 4: case 5: case 6: $cy = 2; $cys = array(4, 5, 6); break;
            case 7: case 8: case 9: $cy = 3; $cys = array(7, 8, 9); break;
        }
        switch ($x) {
            case 1: case 2: case 3: $cx = 0; $cxs = array(1, 2, 3); break;
            case 4: case 5: case 6: $cx = 3; $cxs = array(4, 5, 6); break;
            case 7: case 8: case 9: $cx = 6; $cxs = array(7, 8, 9); break;
        }

        $cube = $cy+$cx;

        switch ($y) {
            case 1: case 4: case 7: $cy = 1; break;
            case 2: case 5: case 8: $cy = 2; break;
            case 3: case 6: case 9: $cy = 3; break;
        }
        switch ($x) {
            case 1: case 4: case 7: $cx = 0; break;
            case 2: case 5: case 8: $cx = 3; break;
            case 3: case 6: case 9: $cx = 6; break;
        }

        $cell = $cy+$cx;

        $cells = array();
        foreach ($cys as $y) {
            foreach ($cxs as $x) {
                $cells[] = array(
                    'x' => $x,
                    'y' => $y,
                );
            }
        } 

        return array(
            'cube' => $cube,
            'cell' => $cell,
            'columns' => $cys,
            'rows' => $cxs,
            'cells' => $cells,
        );
    }

    public function is_solved()
    {
        for ($y = 1; $y <= 9; $y++) {
            if (array_sum($this->s[$y]) != 45) {
                return false;
            }
        }
        return true;
    }

    public function _set_x()
    {
        $this->x = array();
        for ($x = 1; $x <= 9; $x++) {
            for ($y = 1; $y <= 9; $y++) {
                $this->x[$x][$y] = $this->p[$y][$x];
            }
        }
    }

    public function _set_y()
    {
        $this->y = $this->p;
    }

    public function _set_c()
    {
        $this->c = array();
        for($y = 1; $y <= 9; $y++) {
            for($x = 1; $x <= 9; $x++) {
                $c = $this->_get_c_id($y, $x);
                $this->c[$c['cube']][$c['cell']] = $this->p[$y][$x];
            }
        }
    }

    public function _get_possible()
    {
        $possible = array();
        for ($n = 1; $n <= 9; $n++) {
            for ($y = 1; $y <= 9; $y++) {
                for ($x = 1; $x <= 9; $x++) {
                    if ($this->s[$y][$x]) {
                        continue;
                    }
                    $c = $this->_get_c_id($y, $x);
                    if (
                        !in_array($n, $this->y[$y]) &&
                        !in_array($n, $this->x[$x]) &&
                        !in_array($n, $this->c[$c['cube']])
                    ) {
                        $possible[$y][$x][] = $n;
                    }
                }
            }
        }

        return $possible;
    }

    public function _set_s_cell($y, $x, $n) 
    {
        $this->s[$y][$x] = $n;
        $this->y[$y][$x] = $n;
        $this->x[$x][$y] = $n;
        $c = $this->_get_c_id($y, $x);
        $this->c[$c['cube']][$c['cell']] = $n;
    }

    public function _check_possible_single($possible)
    {
        foreach ($possible as $y => $ya) {
            foreach ($ya as $x => $xa) {
                // Check if there is only one possible answer for cell
                if (count($xa) == 1) {
                    $this->_set_s_cell($y, $x, $xa[0]);
                    unset($possible[$y][$x]);
                    if (empty($possible[$y])) {
                        unset ($possible[$y]);
                    }
                    continue;
                }
            }
        }
    }

    public function _check_possible_y($possible)
    {
        // Check if only one instance of n exists for row
        for ($y = 1; $y <= 9; $y++) {
            if (!array_key_exists($y, $possible)) {
                continue;
            }
            for ($n = 1; $n <= 9; $n++) {
                $fx = $fy = $count = 0;
                foreach ($possible[$y] as $x => $xa) {
                    foreach ($xa as $p) {
                        if ($p == $n) {
                            $fx = $x;
                            $fy = $y;
                            $count++;
                        }
                    }
                }
                if ($count == 1) {
                    $this->_set_s_cell($fy, $fx, $n);
                    unset($possible[$fy][$fx]);
                    if (empty($possible[$fy])) {
                        unset($possible[$fy]);
                        continue(2);
                    }
                }                
            }
        }
    }

    public function _check_possible_c($possible) 
    {
        foreach ($possible as $y => $ya) {
            foreach ($ya as $x => $xa) {
                $c = $this->_get_c_id($y, $x);
                for ($n = 1; $n <= 9; $n++) {
                    $fx = $fy = $count = 0;
                    foreach ($c['cells'] as $cell) {
                        if (array_key_exists($cell['y'], $possible) && array_key_exists($cell['x'], $possible[$cell['y']])) {
                            foreach ($possible[$cell['y']][$cell['x']] as $p) {
                                if ($p == $n) {
                                    $fx = $cell['x'];
                                    $fy = $cell['y'];
                                    $count++;
                                }
                            }
                        }
                    }
                    if ($count == 1) {
                        $this->_set_s_cell($fy, $fx, $n);
                        unset ($possible[$fy][$fx]);
                        if (empty($possible[$fy])) {
                            unset($possible[$fy]);
                            continue(2);
                        }
                    }
                }   
            }
        }
    }

    public function _check_possible_x($possible)
    {
        // Create X Y array from Y X array
        $possible_x = array();
        foreach ($possible as $y => $ya) {
            foreach ($ya as $x => $xa) {
                $possible_x[$x][$y] = $xa;
            }
        }
        // Check if only one instance of n exists for col
        for ($x = 1; $x <= 9; $x++) {
            if (!array_key_exists($x, $possible_x)) {
                continue;
            }
            for ($n = 1; $n <= 9; $n++) {
                $fx = $fy = $count = 0;
                foreach ($possible_x[$x] as $y => $ya) {
                    foreach ($ya as $p) {
                        if ($p == $n) {
                            $fx = $x;
                            $fy = $y;
                            $count++;
                        }
                    }
                }
                if ($count == 1) {
                    $this->_set_s_cell($fy, $fx, $n);
                    unset ($possible_x[$fx][$fy]);
                    if (empty($possible_x[$fx])) {
                        unset($possible_x[$fx]);
                        continue(2);
                    }
                }
            }
        }
    }

    public function solve()
    {
        $start = floor(microtime(true) * 1000);
        for ($i = 1; $i <= 1000; $i++) {
            $pre_s = $this->s;
            $this->_check_possible_single(
                $this->_get_possible()
            );
            $this->_check_possible_y(
                $this->_get_possible()
            );
            $this->_check_possible_x(
                $this->_get_possible()
            );
            $this->_check_possible_c(
                $this->_get_possible()
            );

            if ($this->is_solved()) {
                $end = floor(microtime(true) * 1000);
                return array(
                    'resp' => $this->s,
                    'mode' => 'fully_solved',
                    'iterations' => $i,
                    'time' => $end - $start,
                );
                return $this->s;
            }

            if ($pre_s == $this->s) {
                // No changes, cannot solve
                $end = floor(microtime(true) * 1000);

                if ($this->p == $this->s) {
                    return array(
                        'resp' => $this->s,
                        'mode' => 'unsolved',
                        'iterations' => $i,
                        'time' => $end - $start,
                    );
                }
        
                return array(
                    'resp' => $this->s,
                    'mode' => 'partially_solved',
                    'iterations' => $i,
                    'time' => $end - $start,
                );
            }
        }
    }
}