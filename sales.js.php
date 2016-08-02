
<script>

        var id_poup = 'div-popup';

        function load_quotation(quotation_id, quotation_revision_id, status_rev){
                $('#div_quotation').html('<?php echo $AppUI->_('Loading...');?>');
		$('#div_quotation').load('?m=sales&a=vw_quotation&c=view_quotation_detail&suppressHeaders=true',{ quotation_id: quotation_id, quotation_revision_id: quotation_revision_id, status_rev: status_rev });
        }

        function deleteQuotaion(id){

	}

        function load_invoice(invoice_id, invoice_revision_id, status_rev){
                $('#div_invoice').html('<?php echo $AppUI->_('Loading...');?>');
		$('#div_invoice').load('?m=sales&a=vw_invoice&c=view_invoice_detail&suppressHeaders=true', { invoice_id: invoice_id, invoice_revision_id: invoice_revision_id, status_rev: status_rev });
        }

        function check_all(chk) {
            if(document.getElementById("check_all").checked==true) {
                for (i = 0; i < chk.length; i++)
                chk[i].checked = true ;
            } else {
                for (i = 0; i < chk.length; i++)
                chk[i].checked = false ;
            }
        }
        /*
         * danh cho payment
         */
        function load_invoice_payment() {
                $('#div-list-invoice-payment').html('Loading...');
                $('#div-list-invoice-payment').load('?m=sales&a=payment_details&suppressHeaders=true', function() {});
        }

        function NewPaymentList() {
                $('#div_details_payment').html('Loading...');
                $('#div_details_payment').load('?m=sales&a=payment_details&suppressHeaders=true', function() {});
        }


        var js_display_date_format="<?php echo (dPgetConfig('js_display_date_format')? dPgetConfig('js_display_date_format') : dPgetConfig('js_display_date_format_default'))?>";
	var js_input_date_format = "<?php echo (dPgetConfig('js_input_date_format') ? dPgetConfig('js_input_date_format') : dPgetConfig('js_input_date_format_default'))?>";
	$(document).ready(function(){
		bindDatePicker('from_date');
		bindDatePicker('to_date');
		loadJobs();
	});
	function bindDatePicker(id)
	{
		var disp=id+'_display';
		var val=id+'_value';
		$('#'+disp).datepicker({
			showOn: 'button', buttonImage: 'images/calendar.gif', buttonImageOnly: true,
			dateFormat: js_display_date_format,
			changeMonth: true,
			changeYear: true,
			onSelect: function(dateText) {
				$('#'+val).val($.datepicker.formatDate(js_input_date_format, $(this).datepicker('getDate')));
			}
		});
	}
</script>
