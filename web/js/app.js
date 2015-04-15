(function() {
    function searchclear() {
        $('[data-action="clearinput"]').each(function() {
            var $el = $(this);
            var $target = $($el.data('target'));

            function checkShow() {
                $el.toggleClass('show', !!$target.val());
            }

            $el.on('click', function() {
                $target.val('');
                checkShow();
            });

            $target.on('keyup', checkShow);

            checkShow();
        })
    }

    function fastClick() {
        FastClick.attach(document.body);
    }

    function swipebox() {
        $(".img-zoomable").swipebox();
    }

    fastClick();
    searchclear();
    swipebox();
})();