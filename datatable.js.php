<script type="text/javascript">
    var filters = $('#filter_search').val();
    $('#invoice_table_filter label input[type=text]').val(filters).trigger($.Event("keyup", { keyCode: 13 }));
</script>

