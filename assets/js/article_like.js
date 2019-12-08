$(document).ready(function () {
    $('.js-like-article').on('click', function (e) {
        e.preventDefault(); // browser doesn't follow the link

        var $link = $(e.currentTarget);
        //$link.removeClass('fa-thumbs-o-up');
        //$link.addClass('fa-thumbs-up');

        $.ajax({
            method: 'POST',
            url: $link.attr('href')
        }).done(function (data) {
            $('.js-like-article-count').html(data.likes);
        });
        function disableLike(a){
            document.getElementById(a.like).disabled = true;
            alert("Button has been disabled.");
        }
    });
});