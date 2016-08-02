<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
require_once (DP_BASE_DIR."/modules/clients/company_manager.class.php");
require_once (DP_BASE_DIR."/modules/sales/CPaymentManager.php");

$Cpayment = new CPaymentManager();

function __default() {
    global $AppUI;
    echo '<div id="btn-back">';
        back_pay();
    echo '</div>';
    echo $block_info = $AppUI ->createBlock('div_info',  _form_info(),'style="float: left"');
    echo '<br>';
    echo '<div id="form-add-new">';
    echo '</div>';
     }
   //echo $block_note = $AppUI ->createBlock('div_note',_form_button(),'style="float: right;margin-right : 40%"');
function _form_info(){
     global $AppUI; global $Cpayment;
    echo $tittle_block = $AppUI ->createBlock('div_details','<h1>New Payment</h1>','style="text-align: center;text-font:bold; float: left; margin-right : 10 px"');
    echo '<br><div style="width:100%; height: 5px; align: left; background-color: #E2E2E2"></div><br>';
    echo $tittle_block = $AppUI ->createBlock('div_invoice','','style="float: left; margin-left : 40%"');
    if ($click_tab == true) {
            $status_id = null;
            $customer_id = null;
        } else {
            $status_id = $_POST['status'];
            $customer_id = $_POST['customer_id'];
        }

        $invoice_arr = $Cpayment->list_invoice_payment($AppUI->user_id, $customer_id, $status_id);

        $invoice_stt = dPgetSysVal('InvoiceStatus');

        $dataTableRaw = new JDataTable('sales_payment');
        $dataTableRaw->setWidth('100%');
        $dataTableRaw->setHeaders(array('<input type="checkbox" disabled="disabled">','Invoice No', 'Client', 'Description','Date','Total','Status'));

        $colAttributes = array('class="checkbox" width="5%" align="center"', 'class="invoice_no" align="center" width="10%"', 'class="client_invoice" width="20%"','class="invoice_des" width="20%"','class="invoice_date" align="center" width="13%"','class="invoice_total" align="right" width="13%"','class="invoice_status" align="center" width="13%"',);

        //$poup_form_new = _popup_form(true);
        $tableData = array();
        if (count($invoice_arr) > 0 ) {
            foreach ($invoice_arr as $invoice) {
                $tableData[]= array(
                    '<input type="checkbox">',
                   //'<div><a id="'.$invoice['invoice_id'].'" onclick="poup_new('.$invoice['invoice_id'].');" style="cursor:pointer;">'.$invoice['invoice_no'].'</a></div>',
                   '<div><a id="'.$invoice['invoice_id'].'" onclick="load_add('.$invoice['invoice_id'].');" style="cursor:pointer;">'.$invoice['invoice_no'].'</a></div>',
                    $invoice['company_name'],
                    $invoice['invoice_internal_notes'],
                    $invoice['invoice_revision_date'],'$'.
                    $invoice['invoice_revision_currency'].'.00',
                    $invoice_stt[$invoice['invoice_status']]
                );
            }
        }
        $dataTableRaw->setDataRow($tableData, $colAttributes);

        $dataTableRaw->setTableAttributes('class="tbl" cellpadding="2" cellspacing="1"');
        $dataTableRaw->show();
    echo '<br>';
}
/*function _popup_form($to_general = true) {
    global $AppUI;    global $Cpayment;
    $invoice_id = dPgetCleanParam($_GET, 'invoice_id', 0);
    $invoice_revision = $Cpayment->loadInvoiceRevision($invoice_id);
    foreach ($invoice_revision as $revision) {
        $invoice_revision_id = $revision['invoice_revision_id'];
    }
    $_id_form = 'AddstrationForm';
    
    $demonstrationForm = new JFormer($_id_form, array(
            //'submitButtonText' => 'Payment',
            'onSubmitFunctionServerSide' => 'onSubmitPay',
            'action' => '?m=sales&a=payment_details&c=_payment_process&suppressHeaders=true',
    ));
    $demonstrationSection = new JFormSection($demonstrationForm->id . 'Section');

    $demonstrationSection->addJFormComponentArray(array(
         $payment = new JFormComponentSingleLineText('payment', 'Payment:', array(
                             'validationOptions' => array('required', 'integer'),'tip' => 'Please enter payment must to be greater than 0.'
            )),
        $method_dropdown = new JFormComponentDropDown('payment_amount', 'Method:', array(
                array(
                    'value' => '',
                    'label' => '- Select Option -',
                    'disabled' => true,
                    'selected' => true
                ),
                array(
                    'value' => 0,
                    'label' => 'Master Card',
                    'disabled' => false,
                    'selected' => true
                ),
                array(
                    'value' => 1,
                    'label' => 'Visa Card',
                    'disabled' => false,
                    'selected' => true
                ),
                array(
                    'value' => 2,
                    'label' => 'Cash',
                    'disabled' => false,
                    'selected' => true
                ),
            )
        ),
        $date = new JFormComponentDate('payment_date', 'Date: ', array(
            'validationOptions' => array('required'), 'tip' => 'Please enter receipt date',
            'dateFormat' => 'yyyy-mm-dd',
        ) ),
        $note = new JFormComponentTextArea('payment_notes', 'Notes', array(
        )),
         new JFormComponentHidden('form_id', $_id_form),
         new JFormComponentHidden('invoice_id', $_POST['invoice_id']),
         new JFormComponentHidden('invoice_revision_id', $invoice_revision_id),
    ));
    $demonstrationSection->style = 'height: 400';
    $demonstrationForm->style = 'height: 500';
    $demonstrationForm->addJFormSection($demonstrationSection);
    $demonstrationForm->processRequest($to_general);
    $demonstrationForm->clearAllComponentValues();

    $_dialog_form = new JDialog($_id_form, array(
                'autoOpen' => 'false',
                'width' => '700',
    ));
    $_dialog_form->show();
    return $_id_form;
}
 function onSubmitPay($formValues) {

    global $AppUI;  global $Cpayment;

           $Cpayment->add_payment(_payment_class($formValues));
           $Cpayment->add_payment_schedule(_payment_schulude($formValues));

          $date = $formValues->AddstrationFormSection->payment_date;
          $str_date = strtotime($date);
          $rpl_date = date("Y-m-d", $str_date);

         $response = array(
             'successJs' => '$("#'. $formValues->AddstrationFormSection->form_id. '").dialog(\'close\');  '//ajax_open_in_main_body(\'?m=sales&a=vw_payment_schedule&suppressHeaders=true\');

        );

    return $response;

  }
  function _payment_class($formValues){

      global $AppUI;
          $date = $formValues->AddstrationFormSection->payment_date;
          $str_date = strtotime($date);
          $rpl_date = date("Y-m-d H:i:s", $str_date);

          $CpaymentClass = array();
          $CpaymentClass['payment'] = $formValues->AddstrationFormSection->payment;
          $CpaymentClass['invoice_revision_id'] = $formValues->AddstrationFormSection->invoice_revision_id;
          $CpaymentClass['payment_amount'] = $formValues->AddstrationFormSection->payment_amount;
          $CpaymentClass['payment_date'] = $rpl_date;
          $CpaymentClass['payment_notes'] = $formValues->AddstrationFormSection->payment_notes;

      return $CpaymentClass;
  }

  function _payment_schulude($formValues) {

      global $AppUI;
          $date = $formValues->AddstrationFormSection->payment_date;
          $str_date = strtotime($date);
          $rpl_date = date("Y-m-d H:i:s", $str_date);

          $CpaymentSchule = array();
          //$CpaymentSchule['payment'] = $formValues->demonstrationFormSection->payment;
          $CpaymentSchule['invoice_revision_id'] = $formValues->AddstrationFormSection->invoice_revision_id;
          $CpaymentSchule['payment_schedule_paid'] = $formValues->AddstrationFormSection->payment_amount;
          $CpaymentSchule['payment_schedule_paid_date'] = $rpl_date;
          $CpaymentSchule['payment_schedule_notes'] = $formValues->AddstrationFormSection->payment_notes;

      return $CpaymentSchule;
  }

  function _payment_process() {
      _popup_form(false);
  }*/
function back_pay() {
    global $AppUI;
    $new_block = $AppUI ->createBlock('button_back','<button class="ui-button ui-state-default ui-corner-all"  onclick="javascript:history.back();">Back</button>&nbsp;&nbsp;','style="text-align: center; float: left; margin-right : 10 px"');
     echo $button_block = $AppUI ->createBlock('button_block',$new_block,'style="text-align: center; float: left"');
    echo '<br><br>';
}
?>
<script language="javascript">
    
    function load_add(invoice_id) {
            $('#form-add-new').html('Loading...');
            $('#form-add-new').load('?m=sales&a=vw_add_payment&suppressHeaders=true&invoice_id='+invoice_id);
      
    }

    /*function poup_new(invoice_id) {
        $('#AddstrationForm').html('Loading...');
        $('#AddstrationForm').load('?m=sales&a=payment_details&c=_popup_form&suppressHeaders=true&invoice_id='+invoice_id).dialog({
                            resizable: false,
                            modal: true,
                            title: '<?php echo $AppUI->_('New Item'); ?>',
                            width: 450,
                            maxheight: 'auto',
                            close: function(ev,ui){$(this).html('');}
            });
           $('#AddstrationForm').dialog('open');
       }*/
    
</script>
