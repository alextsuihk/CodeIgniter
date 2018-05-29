
<script>
$(document).on("click", ".open-Dialog", function () {
    var title     = $(this).data('title');
    var id        = $(this).data('id');
    var customer  = $(this).data('customer');
    var disti     = $(this).data('disti');
    $("#title_approve").val( title );
    $("#title_reject").val( title );
    $(".modal-body #id").val( id );
    $(".modal-body #customer").val( customer );
    $(".modal-body #disti").val( disti );
});
</script>

