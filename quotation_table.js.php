<script type="text/javascript">
	$(document).ready(function() {
                var count =$('#quotation_count').val();
                var oTable = $('#detail_quotation_table').dataTable();
                var tr = '<tfoot id="add-quo-inline-foot">';
                tr += '<input type="hidden" id="count_row_item" value="'+(++count)+'">';
                tr += '</tfoot>';
                $('#detail_quotation_table').append(tr);
                
//                $('#quotation_sortable').sortable({
//                    update: function() { 
//                        console.log('update');
//                        var order = $('#quotation_sortable').sortable('serialize');
//                        $.post('?m=sales&a=vw_quotation&c=_do_update_quotation_order&suppressHeaders=true',order,"json");
//                    },
//                    delay: 30
//                 });
                $('#quotation_sortable').sortable({
                    forcePlaceholderSize: true,
                    forceHelperSize: true,
                    items: 'tr',
                    update : function () {
                        var quotation_id = $('#quo_id').val();
                        var quotation_rev_id = $('#quo_rev_id').val();
                        var serial = $('#quotation_sortable').sortable('serialize', {key: 'row_item[]', attribute: 'id'});
                        serial+="&quotation_id="+quotation_id+"&quotation_rev_id="+quotation_rev_id;
//                        alert(serial);
                        $.ajax({
                            'url': '?m=sales&a=vw_quotation&c=_do_update_quotation_order&suppressHeaders=true',
                            'type': 'post',
                            'data': serial,
                            'success': function(data){
                                $('#div_3').load('?m=sales&a=vw_quotation&c=load_html_table&suppressHeaders=true',{quotation_id:quotation_id,quotation_rev_id:quotation_rev_id});
                                $('#div_total_note').load('?m=sales&a=vw_quotation&c=load_form_total&suppressHeaders=true',{quotation_id:quotation_id,quotation_rev_id:quotation_rev_id});
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
