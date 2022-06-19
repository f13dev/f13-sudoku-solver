<?php namespace F13\SudokuSolver\Models ;

class Model
{
    private $columns;
    private $rows; 
    private $squares;
    private $puzzle;
    private $solved;

    public function __construct($sudoku)
    {
        $this->puzzle = $sudoku;
        $this->solved = $sudoku;
        $this->_set_rows();
        $this->_set_columns();
        $this->_set_squares();
    }

    /**
     * Validate rows within a puzzle, checking for duplicate numbers
     * 
     * @return Bool Validated
     */
    public function _validate_rows()
    {
        foreach ($this->rows as $row) {
            for ($number = 1; $number <= 9; $number++) {
                $count = 0;
                foreach ($row as $cell) {
                    if ($cell == $number) {
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

    /**
     * Validate columns within a puzzle, checking for duplicate numbers
     * 
     * @return Bool Validated
     */
    public function _validate_columns()
    {
        foreach ($this->columns as $column) {
            for ($number = 1; $number <= 9; $number++) {
                $count = 0;
                foreach ($column as $cell) {
                    if ($cell == $number) {
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

    /**
     * Validate squares within a puzzle, checking for duplicate numbers
     * 
     * @return Bool Validated
     */
    public function _validate_squares() 
    {
        foreach ($this->squares as $square) {
            for ($number = 1; $number <= 9; $number++) {
                $count = 0;
                foreach ($square as $cell) {
                    if ($cell == $number) {
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

    /**
     * Validate a puzzle by checking for mutliple instances of n
     * in a column, row or square
     * 
     * @return Bool Validated
     */
    public function validate_puzzle()
    {
        return (
            $this->_validate_columns() &&
            $this->_validate_rows() &&
            $this->_validate_squares()
        );
    }

    /**
     * Get the X Y coordinates from a cell within a square
     * 
     * @param Int $cube Numeric cube identifier
     * @param Int $cell Numeric cell identifier
     * 
     * @return Array
     *     Int x column coordinate
     *     Int y row coordinate
     */
    public function _get_row_column_from_square_cell($square, $cell)
    {
        switch ($square) {
            case 1: case 4: case 7: $square_columns = array(1,2,3); break;
            case 2: case 5: case 8: $square_columns = array(4,5,6); break;
            case 3: case 6: case 9: $square_columns = array(7,8,9); break;
        }
        switch ($square) {
            case 1: case 2: case 3: $square_rows = array(1,2,3); break;
            case 4: case 5: case 6: $square_rows = array(4,5,6); break;
            case 7: case 8: case 9: $square_rows = array(7,8,9); break;
        }

        switch ($cell) {
            case 1: case 2: case 3: $column = $square_columns[0]; break;
            case 4: case 5: case 6: $column = $square_columns[1]; break;
            case 7: case 8: case 9: $column = $square_columns[2]; break;
        }

        switch ($cell) {
            case 1: case 4: case 7: $row = $square_rows[0]; break;
            case 2: case 5: case 8: $row = $square_rows[1]; break;
            case 3: case 6: case 8: $row = $square_rows[2]; break;
        }

        return array(
            'column' => $column,
            'row' => $row,
        );
    }

    /**
     * Get the square and cell identifier from an Y, X coordinate
     * 
     * @param Int $y Y coordinate
     * @param Int $x X coordinate
     * 
     * @return Array
     *     Int cube Cube identifier
     *     Int cell Cell identifier
     *     Array columns
     *         Int Columns that pass through square
     *     Array rows
     *         Int Rows that pass through square
     *     Array cells
     *         Int y Cell y position
     *         Int x Cell x position
     */
    public function _get_square_from_row_column($row, $column)
    {
        switch ($row) {
            case 1: case 2: case 3: $cy = 1; $cys = array(1, 2, 3); break;
            case 4: case 5: case 6: $cy = 2; $cys = array(4, 5, 6); break;
            case 7: case 8: case 9: $cy = 3; $cys = array(7, 8, 9); break;
        }
        switch ($column) {
            case 1: case 2: case 3: $cx = 0; $cxs = array(1, 2, 3); break;
            case 4: case 5: case 6: $cx = 3; $cxs = array(4, 5, 6); break;
            case 7: case 8: case 9: $cx = 6; $cxs = array(7, 8, 9); break;
        }

        $cube = $cy+$cx;

        switch ($row) {
            case 1: case 4: case 7: $cy = 1; break;
            case 2: case 5: case 8: $cy = 2; break;
            case 3: case 6: case 9: $cy = 3; break;
        }
        switch ($column) {
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

    /**
     * Check if a puzzle is solved
     * 
     * @return Bool Puzzle is solved
     */
    public function is_solved()
    {
        for ($row = 1; $row <= 9; $row++) {
            if (array_sum($this->solved[$row]) != 45) {
                return false;
            }
        }
        return true;
    }

    /**
     * Set the locally stored x axis array
     */
    public function _set_columns()
    {
        $this->columns = array();
        for ($column = 1; $column <= 9; $column++) {
            for ($row = 1; $row <= 9; $row++) {
                $this->columns[$column][$row] = $this->puzzle[$row][$column];
            }
        }
    }

    /**
     * Set the locally stored y axis array
     */
    public function _set_rows()
    {
        $this->rows = $this->puzzle;
    }

    /**
     * Set the locally stored square array
     */
    public function _set_squares()
    {
        $this->squares = array();
        for($row = 1; $row <= 9; $row++) {
            for($column = 1; $column <= 9; $column++) {
                $c = $this->_get_square_from_row_column($row, $column);
                $this->squares[$c['cube']][$c['cell']] = $this->puzzle[$row][$column];
            }
        }
    }

    /**
     * Find possible numbers for each empty cell
     * 
     * @return Array
     *     Int y => Array
     *         Int x => Array
     *             Int Possible number for cell
     */
    public function _get_possible()
    {
        $possible = array();
        for ($number = 1; $number <= 9; $number++) {
            for ($row = 1; $row <= 9; $row++) {
                for ($column = 1; $column <= 9; $column++) {
                    if ($this->solved[$row][$column]) {
                        continue;
                    }
                    $c = $this->_get_square_from_row_column($row, $column);
                    if (
                        !in_array($number, $this->rows[$row]) &&
                        !in_array($number, $this->columns[$column]) &&
                        !in_array($number, $this->squares[$c['cube']])
                    ) {
                        $possible[$row][$column][] = $number;
                    }
                }
            }
        }

        return $possible;
    }

    /**
     * Sets a cell that has been found
     */
    public function _set_cell_solved($row, $column, $number) 
    {
        $this->solved[$row][$column] = $number;
        $this->rows[$row][$column] = $number;
        $this->columns[$column][$row] = $number;
        $c = $this->_get_square_from_row_column($row, $column);
        $this->squares[$c['cube']][$c['cell']] = $number;
    }

    /**
     * Checks for any cells with only one possible value
     * 
     * @param Array $possible
     */
    public function _check_possible_single()
    {
        $possible = $this->_get_possible();
        foreach ($possible as $row => $columns) {
            foreach ($columns as $column => $numbers) {
                // Check if there is only one possible answer for cell
                if (count($numbers) == 1) {
                    $this->_set_cell_solved($row, $column, $numbers[0]);
                    unset($possible[$row][$column]);
                    if (empty($possible[$row])) {
                        unset ($possible[$row]);
                    }
                    continue;
                }
            }
        }
    }

    /**
     * Checks for any Columns where a possible number shows only once
     * 
     * @param Array $possible
     */
    public function _check_possible_in_row()
    {
        $possible = $this->_get_possible();
        // Check if only one instance of n exists for row
        for ($row = 1; $row <= 9; $row++) {
            if (!array_key_exists($row, $possible)) {
                continue;
            }
            for ($number = 1; $number <= 9; $number++) {
                $found_column = $found_row = $count = 0;
                foreach ($possible[$row] as $column => $numbers) {
                    foreach ($numbers as $possible_number) {
                        if ($possible_number == $number) {
                            $found_column = $column;
                            $found_row = $row;
                            $count++;
                        }
                    }
                }
                if ($count == 1) {
                    $this->_set_cell_solved($found_row, $found_column, $number);
                    unset($possible[$found_row][$found_column]);
                    if (empty($possible[$found_row])) {
                        unset($possible[$found_row]);
                        continue(2);
                    }
                }                
            }
        }
    }

    /**
     * Check for any squares where a possible number shows only once
     * 
     * @param Array $possible
     */
    public function _check_possible_in_square() 
    {
        $possible = $this->_get_possible();
        foreach ($possible as $row => $columns) {
            foreach ($columns as $column => $cells) {
                $square = $this->_get_square_from_row_column($row, $column);
                for ($number = 1; $number <= 9; $number++) {
                    $found_column = $found_row = $count = 0;
                    foreach ($square['cells'] as $cell) {
                        if (array_key_exists($cell['y'], $possible) && array_key_exists($cell['x'], $possible[$cell['y']])) {
                            foreach ($possible[$cell['y']][$cell['x']] as $possible_number) {
                                if ($possible_number == $number) {
                                    $found_column = $cell['x'];
                                    $found_row = $cell['y'];
                                    $count++;
                                }
                            }
                        }
                    }
                    if ($count == 1) {
                        $this->_set_cell_solved($found_row, $found_column, $number);
                        unset ($possible[$found_row][$found_column]);
                        if (empty($possible[$found_row])) {
                            unset($possible[$found_row]);
                            continue(2);
                        }
                    }
                }   
            }
        }
    }

    /**
     * Check for any columns where a possible number shows only once
     * 
     * @param Array $possible
     */
    public function _check_possible_in_column()
    {
        $possible = $this->_get_possible();
        // Create X Y array from Y X array
        $possible_row = array();
        foreach ($possible as $row => $columns) {
            foreach ($columns as $column => $numbers) {
                $possible_row[$column][$row] = $numbers;
            }
        }
        // Check if only one instance of n exists for col
        for ($column = 1; $column <= 9; $column++) {
            if (!array_key_exists($column, $possible_row)) {
                continue;
            }
            for ($number = 1; $number <= 9; $number++) {
                $found_column = $found_row = $count = 0;
                foreach ($possible_row[$column] as $row => $columns) {
                    foreach ($columns as $possible_number) {
                        if ($possible_number == $number) {
                            $found_column = $column;
                            $found_row = $row;
                            $count++;
                        }
                    }
                }
                if ($count == 1) {
                    $this->_set_cell_solved($found_row, $found_column, $number);
                    unset ($possible_row[$found_column][$found_row]);
                    if (empty($possible_row[$found_column])) {
                        unset($possible_row[$found_column]);
                        continue(2);
                    }
                }
            }
        }
    }

    /**
     * Solve the loaded puzzle
     * 
     * @return Array
     *     Array resp The solved puzzle
     *     String mode The completion mode
     *     Int iteration The number of iterations for completing the puzzle
     *     Int time Completion time in milliseconds
     */
    public function solve()
    {
        $start = floor(microtime(true) * 1000);
        for ($i = 1; $i <= 1000; $i++) {
            $pre_solved = $this->solved;
            $this->_check_possible_single();
            $this->_check_possible_in_column();
            $this->_check_possible_in_row();
            $this->_check_possible_in_square();

            if ($this->is_solved()) {
                $end = floor(microtime(true) * 1000);
                return array(
                    'resp' => $this->solved,
                    'mode' => 'fully_solved',
                    'iterations' => $i,
                    'time' => $end - $start,
                );
                return $this->solved;
            }

            if ($pre_solved == $this->solved) {
                // No changes, cannot solve
                $end = floor(microtime(true) * 1000);

                if ($this->puzzle == $this->solved) {
                    return array(
                        'resp' => $this->solved,
                        'mode' => 'unsolved',
                        'iterations' => $i,
                        'time' => $end - $start,
                    );
                }
        
                return array(
                    'resp' => $this->solved,
                    'mode' => 'partially_solved',
                    'iterations' => $i,
                    'time' => $end - $start,
                );
            }
        }
    }
}