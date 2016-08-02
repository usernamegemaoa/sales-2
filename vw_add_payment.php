<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
require_once (DP_BASE_DIR."/modules/clients/company_manager.class.php");
require_once (DP_BASE_DIR."/modules/sales/CPaymentManager.php");


$Cpayment = new CPaymentManager();

function __default() {
    global $AppUI;
    _form_new(true);
}
function _form_info($invoice_id){
     global $AppUI; global $Cpayment;
     
        $invoice_arr = $Cpayment->loadInvoiceRevision($invoice_id);
        foreach ($invoice_arr as $invoice) {
            $invoice_no = $invoice['invoice_no'];
        }
        
    echo $tittle_block = $AppUI ->createBlock('div_details','<h1>Payment for this invoice '.$invoice_no.'</h1>','style="text-align: center;text-font:bold; float: left; margin-right : 10 px"');
    echo '<br><div style="width:60%; height: 5px; align: left; background-color: #E2E2E2"></div><br>';
    echo '<br>';
}

function _form_new($to_generate = true) {
    global $AppUI;  global $Cpayment;
    /*$method = dPgetSysVal('PaymentMethod');
    foreach ($method as $key => $value) {
    }*/
    $_from_new_id = 'demonstrationForm';
    $invoice_id = dPgetCleanParam($_GET, 'invoice_id', 0);
    $invoice_revision = $Cpayment->loadInvoiceRevision($invoice_id);
    foreach ($invoice_revision as $revision) {
        $invoice_revision_id = $revision['invoice_revision_id'];
        $invoice_total = $revision['invoice_revision_currency'];
    }
    echo $block_info = $AppUI ->createBlock('div_info',  _form_info($invoice_id),'style="float: left"');
   

    $payment_schudule = new JFormer($_from_new_id,
        array(
            'submitButtonText' => 'Payment',
            'onSubmitFunctionServerSide' => 'onSubmit',
            'action' => '?m=sales&a=vw_add_payment&c=_payment_process&suppressHeaders=true',
    ));
    
    $demonstrationSection = new JFormSection($payment_schudule->id . 'Section');
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
            'dateFormat' => 'Y-m-d H:i:s',
        ) ),
        $note = new JFormComponentTextArea('payment_notes', 'Notes', array(
        )),
         new JFormComponentHidden('invoice_id', $invoice_id),
         new JFormComponentHidden('invoice_revision_id', $invoice_revision_id),
         new JFormComponentHidden('invoice_revision_currency', $invoice_total),
         new JFormComponentHidden('form_id', $_from_new_id),
     ));
    
        $demonstrationSection->style = 'height: 400';
        $payment_schudule->style = 'height: 500';
        
        $payment_schudule->addJFormSection($demonstrationSection);
        $payment_schudule->processRequest($to_generate);
        $payment_schudule->clearAllComponentValues();
}
  
  function onSubmit($formValues) {
      
    global $AppUI;  global $Cpayment;
    
           $Cpayment->add_payment(_payment_class($formValues));
           $Cpayment->add_payment_schedule(_payment_schulude($formValues));

          $date = $formValues->demonstrationFormSection->payment_date;
          $invoice_id = $formValues->demonstrationFormSection->invoice_id;
          $rpl_date = date('Y-m-d');//, strtotime('22/01/1970')
          
          $total = $formValues->demonstrationFormSection->invoice_revision_currency;
          $total_paid = $formValues->demonstrationFormSection->payment;
          //if ($total_paid > $total) {
              $bland = $total_paid - $total;
          /*}else {
              $bland = 0;
          }*/

         $response = array('successPageHtml' =>
             '<h5><font color="blue"><u>Summary</u></font></h5>
             <h4>
                 <span id="total" value="'.$total_paid.'"><b>Total Paid:</b> $'.$total_paid.'.00<br/></span>
                 <span id="bland" value="'.$bland.'"><b>Balance: </b> $'.$bland.'.00</span>
             </h4><br/>
             <button class="ui-button ui-state-default ui-corner-all" onclick="load_payment_details('.$invoice_id.');">View Payment</button>',
             
         );
    return $response;

  }
  function _payment_class($formValues){
      
      global $AppUI;
          $rpl_date = date('Y-m-d');
          
          $CpaymentClass = array();
          $CpaymentClass['payment_amount'] = $formValues->demonstrationFormSection->payment;
          $CpaymentClass['invoice_revision_id'] = $formValues->demonstrationFormSection->invoice_revision_id;
          $CpaymentClass['payment_method'] = $formValues->demonstrationFormSection->payment_amount;
          $CpaymentClass['payment_date'] = $rpl_date;
          $CpaymentClass['payment_notes'] = $formValues->demonstrationFormSection->payment_notes;
          
      return $CpaymentClass;
  }
  
  function _payment_schulude($formValues) {

      global $AppUI;
          $rpl_date = date('Y-m-d');

          
          $CpaymentSchule = array();
          $CpaymentSchule['invoice_revision_id'] = $formValues->demonstrationFormSection->invoice_revision_id;
          $CpaymentSchule['payment_schedule_paid'] = $formValues->demonstrationFormSection->payment;
          $CpaymentSchule['payment_schedule_paid_date'] = $rpl_date;
          $CpaymentSchule['payment_schedule_notes'] = $formValues->demonstrationFormSection->payment_notes;

      return $CpaymentSchule;
  }
  
  function _payment_process() {
      _form_new(false);
  }

?>
