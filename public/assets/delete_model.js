function smIsEmpty(n){
    return !(!!n ? typeof n === 'object' ? Array.isArray(n) ? !!n.length : !!Object.keys(n).length : true : false);
}
$(document).ready(function () {
    $('.delete-modal').on('click', function () {
        var link = $(this).attr('link');
        $('.get_link').attr('action', link);
    });
    $("body").on("submit", "form", function() {
        $(this).submit(function() {
            return false;
        });
        return true;
    });
});
