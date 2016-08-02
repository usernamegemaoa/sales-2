<script type="text/javascript">
	$(document).ready(function() {
                var oTable = $('#detail_invoice_table').dataTable();
                var tr = '<tfoot id="add-invoi-inline-foot">';
                tr += '<input type="hidden" id="count_row_item" value="1">';
                tr += '</tfoot>';
                $('#detail_invoice_table').append(tr);
                
                $('#invoice_sortable').sortable({
                    forcePlaceholderSize: true,
                    forceHelperSize: true,
                    items: 'tr',
                    update : function () {
                        var invoice_id = $('#inv_id').val();
                        var invoice_rev_id = $('#inv_rev_id').val();
                        var serial = $('#invoice_sortable').sortable('serialize', {key: 'row_item[]', attribute: 'id'});
                        alert(serial);
                        serial+="&invoice_id="+invoice_id+"&invoice_rev_id="+invoice_rev_id;
//                        alert(serial);
                        $.ajax({
                            'url': '?m=sales&a=vw_invoice&c=_do_update_invoice_order&suppressHeaders=true',
                            'type': 'post',
                            'data': serial,
                            'success': function(data){
                                $('#div_inv_3').load('?m=sales&a=vw_invoice&c=load_inv_html_table&suppressHeaders=true',{invoice_id:invoice_id,invoice_rev_id:invoice_rev_id});
                                $('#div_inv_total_note').load('?m=sales&a=vw_invoice&c=load_form_inv_total&suppressHeaders=true',{invoice_id:invoice_id,invoice_rev_id:invoice_rev_id});
                            },
                            'error': function(request, status, error){
                                alert('Error.');
                            }
                        });
                    },
                    delay: 30
                 }).disableSelection();

	});
</script>
