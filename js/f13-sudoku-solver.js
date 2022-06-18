jQuery(document).ready(function($) {

    $(document).on('submit', '.f13-sudoku-ajax', function(e) {
        e.preventDefault();

        var formData = new FormData(this);
        var target = '#'+$(this).data('target');
        var url = $(this).data('url');

        $(target).prepend('<div class="f13-sudoku-solver-loading"></div>');

        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            processData: false,
            contentType: false,
        }).done(function(data) {
            $(target).html(data);
        }).error(function(error) {
            alert('An error has occured.');
            $('.f13-sudoku-solver-loading').remove();
        });
    });

});