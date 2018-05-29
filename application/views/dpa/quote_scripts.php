
<script>
$(document).on("click", ".open-RequestDialog", function () {
    var title   = $(this).data('title');
    var id      = $(this).data('id');
    var status  = $(this).data('status');
    var wording = $(this).data('wording');
    $("#title_approve").val( title );
    $("#title_reject").val( title );
    $(".modal-body #id").val( id );
    $(".modal-body #status").val( status );
    $(".modal-body #wording").val( wording );
});
</script>

