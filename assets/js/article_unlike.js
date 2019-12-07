$(document).ready(function () {
    $('.js-unlike-article').on('click', function (e) {
        e.preventDefault(); // browser doesn't follow the link

        var $link = $(e.currentTarget);
        $link.toggleClass('fa-heart').toggleClass('fa-heart-o');

        $.ajax({
            method: 'POST',
            url: $link.attr('href')
        }).done(function (data) {
            $('.js-like-article-count').html(data.likes);
        })
    });
});