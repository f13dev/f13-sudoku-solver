# Sudoku Solver
## Requirements
* WordPress, tested with 6.0, will likely work with older versions
* PHP 7.3 or higher

## Installation
1. Upload to your wp-content/plugins directory
2. Activate plugin in wp-admin
3. Add shortcode [sudoku-solver] to your desired page or post

## Usage
1. Enter numbers from a Sudoku Puzzle or click "Load demo" to load a random puzzle from the small selection of pre-defined puzzles
2. Click "Solve"
     - The puzzle will be solved
     - The puzzle will be partially solved, in cases where the algorithm cannot complete the puzzle
     - The puzzle will not be solved, in cases where the algorithm cannot complete any cells in the puzzle
     - An error will show if the puzzle is not valid, i.e. the same number appears more than once in a row, column or square