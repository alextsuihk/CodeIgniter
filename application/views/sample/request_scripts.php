
<script>
$(document).on("click", ".open-RequestDialog", function () {
    var title   = $(this).data('title');
    var id      = $(this).data('id');
    var status  = $(this).data('status');
    var qty     = $(this).data('qty');
    var wording = $(this).data('wording');
    var address = $(this).data('address');
    $("#title_approve").val( title );
    $("#title_reject").val( title );
    $("#title_grab").val( title );
    $("#title_ship").val( title );
    $(".modal-body #id").val( id );
    $(".modal-body #status").val( status );
    $(".modal-body #qty").val( qty );
    $(".modal-body #wording").val( wording );
    $(".modal-body #address").val( address );
});
</script>

<script>
$(document).on("click", ".open-InventoryCommentDialog", function () {
    var title   = $(this).data('title');
    var comment   = $(this).data('comment');
   $("#title").val( title );
    $(".modal-body #comment").val( comment );
});
</script>