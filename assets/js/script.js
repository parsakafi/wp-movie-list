(function ($) {
    var movieSelect = $('.movie-select2'),
        wpAjax = false,
        movieResult = $('.movie-result');

    if (movieSelect.length > 0) {
        movieSelect.select2({
            placeholder: 'Select an option', width: 'resolve', maximumSelectionLength: 2, allowClear: true
        });

        movieSelect.on('change', function (e) {
            $('#movie_paged').val(1);
            movieSearch();
        });

        setTimeout(function () {
            moviePagination();
        }, 1000);

        function movieSearch() {
            if (wpAjax) return;

            var selectedArtist = [], selectedGenre = [],
                perPage = $('#movie_per_page').val(),
                paged = $('#movie_paged').val(),
                movieList = $('.movie-list'),
                movieLoader = $('.movie-loader');

            movieResult = $('.movie-result');

            movieLoader.show();
            movieList.hide();

            $("#movie_artist :selected").each(function () {
                selectedArtist.push($(this).val());
            });
            $("#movie_genre :selected").each(function () {
                selectedGenre.push($(this).val());
            });

            wpAjax = true;

            $.post(movieListVars.ajaxurl, {
                nonce: movieListVars.nonce,
                action: 'movie_list_search',
                artists: selectedArtist.join(','),
                genres: selectedGenre.join(','),
                paged: paged,
                per_page: perPage
            })
                .done(function (data) {
                    movieLoader.hide();

                    movieResult.replaceWith(data.data.result);
                    setTimeout(function () {
                        moviePagination();
                    }, 500);
                })
                .fail(function (xhr, status, error) {

                })
                .always(function () {
                    wpAjax = false;
                });
        }

        function moviePagination() {
            $('.movie-pagination a').on('click', function (e) {
                e.preventDefault();

                $('#movie_paged').val($(this).attr('href').split('paged=')[1]);
                $([document.documentElement, document.body]).animate({
                    scrollTop: $(".movie-wrapper").offset().top - 100
                }, 500);

                movieSearch();
            });
        }
    }
})(jQuery);