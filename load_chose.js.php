<script type="text/javascript">
    $(document).ready(function() {
 //Load chosen
//        $('#inv_address_id').chosen({allow_single_deselect:true});
//        $('#inv_job_location_id').chosen();
//        $('#address_id').chosen();
//        $('#job_location_id').chosen();
//        $('#int_invoice_CO').chosen();
//        $('#quotation_co').chosen();
//        $('#inv_customer_id').chosen();
        $('#inv_customer_id_chzn').css({'top':'7px'});
        $('#address_atatement').chosen();
        $('#address_atatement_chzn').css({'top':'8px','width':'455px'});
        $('#address_atatement_chzn input').css({'width':'445px'});
        $('#select_quo_statement_address').chosen();
        $('#select_quo_statement_address_chzn').css({'top':'8px','width':'500px'});
        $('#select_quo_statement_address_chzn input').css({'width':'500px'});
        loadJQueryCalendar('sta_from', 'hdd_sta_from', 'dd/mm/yy', 'yy-mm-dd');
        loadJQueryCalendar('sta_to', 'hdd_sta_to', 'dd/mm/yy', 'yy-mm-dd');
        
// Load Select2
        $('#quotation_co').select2({placeholder: "Select a CO"});
        $('#address_id').select2({placeholder: "Select a Address"});
        $('#job_location_id').select2({placeholder: "Select a Job location"});

        $('#sel_customer .select2-container').css({'top':'8px'});
        $('.select2-container').css({'width':'393px'});
        $('#quo_sel_address .select2-container').css({'width':'500px'});
        $('#quo_sel_location .select2-container').css({'width':'500px'});
        $('#select_contract_no').select2({placeholder: "Select contracts no"});
        $('#s2id_select_contract_no').css({'width':'393px'});
        $('#s2id_inv_address_id').css({'width':'393px'});
        $('.select2-container-multi input').css({'width':'390px','height':'26px'});
        
        
        $('#inv_address_id').select2();
        $('#inv_job_location_id').select2();
        $('#int_invoice_CO').select2();
        $('#sel_customer .select2-container').css({'top':'8px'});
        //$('.select2-container').css({'width':'393px'});
        $('#sel_address .select2-container').css({'width':'500px'});
        $('#sel_job_location .select2-container').css({'width':'500px'});
    });
</script>