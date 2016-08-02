<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
require_once(DP_BASE_DIR."/modules/clients/company_manager.class.php");
require_once (DP_BASE_DIR."/modules/sales/CInvoiceManager.php");
require_once (DP_BASE_DIR. '/modules/system/cconfig.class.php');
//require_once (DP_BASE_DIR."/modules/sales/CSalesManager.php");
//
//$CSalesManager = new CSalesManager();
$CInvoiceManager = new CInvoiceManager();
$CCompany = new CompanyManager();

//
$customer_arr=$CInvoiceManager->get_customer();
$config_type5=new CConfigNew();
$term_err = dPgetSysVal('Term');


/** Error reporting
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');*/

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once (DP_BASE_DIR . '/lib/PHPExcel/Classes/PHPExcel.php');


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");

for ($col = 'A'; $col != 'CW'; $col++) {
       $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
}



// Add some data
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:CW1');
$objPHPExcel->getActiveSheet()->getStyle('A2:CW2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A2:CW2')->getFill()->getStartColor()->setARGB('#CCCCCC');
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'DP Info');
$objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A2', 'DebtorCode')
            ->setCellValue('B2', 'CustomerRef')
            ->setCellValue('C2', 'DebtorID')
            ->setCellValue('D2', 'DebtorIDType')
            ->setCellValue('E2', 'Name')
            ->setCellValue('F2', 'AcctOpenDate')
            ->setCellValue('G2', 'Type')
            ->setCellValue('H2', 'Salute')
            ->setCellValue('I2', 'Gender')
            ->setCellValue('J2', 'DOB')
            ->setCellValue('K2', 'MaritalStatus')
            ->setCellValue('L2', 'Nationality')
            ->setCellValue('M2', 'Race')
            ->setCellValue('N2', 'LastEmployer')
            ->setCellValue('O2', 'PrevEmployer')
            ->setCellValue('P2', 'Occupation')
            ->setCellValue('Q2', 'ContactPerson')
            ->setCellValue('R2', 'Department')
            ->setCellValue('S2', 'Annual Income')
            ->setCellValue('T2', 'Address')
            ->setCellValue('U2', 'Country')
            ->setCellValue('V2', 'ContactNo 1')
            ->setCellValue('W2', 'ContactNo 2')
            ->setCellValue('X2', 'ContactNo 3')
            ->setCellValue('Y2', 'ContactNo 4')
            ->setCellValue('Z2', 'Email')
                    ->setCellValue('AA2', 'Currency')
            ->setCellValue('AB2', 'AmountDue')
            ->setCellValue('AC2', 'Amount0')
            ->setCellValue('AD2', 'Amount1')
            ->setCellValue('AE2', 'Amount2')
            ->setCellValue('AF2', 'Amount3')
            ->setCellValue('AG2', 'Amount4')
            ->setCellValue('AH2', 'Amount5')
            ->setCellValue('AI2', 'Amount6')
            ->setCellValue('AJ2', 'Credit / Payment Term')
            ->setCellValue('AK2', 'CreditLimit')
            ->setCellValue('AL2', 'LoanAmt')
            ->setCellValue('AM2', 'LoanOSAmt')
            ->setCellValue('AN2', 'InstalmtAmt')
            ->setCellValue('AO2', 'Obligation')
            ->setCellValue('AP2', 'Vehicle No.')
            ->setCellValue('AQ2', 'Year of Reg/Incorp')
            ->setCellValue('AR2', 'Principal Activities')
            ->setCellValue('AS2', 'Capital Structure')
            ->setCellValue('AT2', 'Charge Details')
            ->setCellValue('AU2', 'Shareholder Details')
            ->setCellValue('AV2', 'FORMTITLE')
            ->setCellValue('AW2', 'Records with')//cHU Y
            ->setCellValue('AX2', 'INVNUMBER')
            ->setCellValue('AY2', 'CUSTNUMBER')
            ->setCellValue('AZ2', 'STFIRSTNM')
                    ->setCellValue('BA2', 'STLASTNM')
            ->setCellValue('BB2', 'STCOMPANY')
            ->setCellValue('BC2', 'STADDRESS1')
            ->setCellValue('BD2', 'STADDRESS2')
            ->setCellValue('BE2', 'STCITY')
            ->setCellValue('BF2', 'STSTATE')
            ->setCellValue('BG2', 'STZIP')
            ->setCellValue('BH2', 'STCOUNTRY')
            ->setCellValue('BI2', 'CUSTDISCOU')
            ->setCellValue('BJ2', 'GICOL1CON')
            ->setCellValue('BK2', 'GICOL3CON')
            ->setCellValue('BL2', 'GICOL4CON')
            ->setCellValue('BM2', 'GICOL5CON')
            ->setCellValue('BN2', 'GICOL6CON')
            ->setCellValue('BO2', 'TAX1RATE')
            ->setCellValue('BP2', 'TAX2RATE')
            ->setCellValue('BQ2', 'SUBTOTAL')
            ->setCellValue('BR2', 'TAXTOTAL1')
            ->setCellValue('BS2', 'TAXTOTAL2')
            ->setCellValue('BT2', 'TAX1AMOUNT')
            ->setCellValue('BU2', 'TAX2AMOUNT')
            ->setCellValue('BV2', 'TOTAL')
            ->setCellValue('BW2', 'AMOUNTPAID')
            ->setCellValue('BX2', 'INVCREDLIM')
            ->setCellValue('BY2', 'INVPRINTED')
            ->setCellValue('BZ2', 'DATPRINTED')
                    ->setCellValue('CA2', 'Records with')//cHU Y
            ->setCellValue('CB2', 'DATVOIDED')
            ->setCellValue('CC2', 'INVPAID')
            ->setCellValue('CD2', 'DATPAID')
            ->setCellValue('CE2', 'Records with')//cHU Y
            ->setCellValue('CF2', 'DATDELETED')
            ->setCellValue('CG2', 'MEMO')
            ->setCellValue('CH2', 'FORMKEY')
            ->setCellValue('CI2', 'TAXNAME1')
            ->setCellValue('CJ2', 'TAXNAME2')
            ->setCellValue('CK2', 'INVEMAILED')
            ->setCellValue('CL2', 'DATEMAILED')
            ->setCellValue('CM2', 'EXTRA1')
            ->setCellValue('CN2', 'EXTRA2')
            ->setCellValue('CO2', 'EXTRA3')
            ->setCellValue('CP2', 'EXTRA4')
            ->setCellValue('CQ2', 'EXTRA5')
            ->setCellValue('CR2', 'SHIPCOST')
            ->setCellValue('CS2', 'OVERDUEDAY')
            ->setCellValue('CT2', 'LATECHARGE')
            ->setCellValue('CU2', 'INTERRATE')
            ->setCellValue('CV2', 'RELAFIELD')
            ->setCellValue('CW2', 'RELACREDAT');


// Miscellaneous glyphs, UTF-8
$i=2;
foreach ($customer_arr as $customer_row) {
   $i++;
   $debtorIDType = "NRIC";
   $type="I";
   if($customer_row['company_title']==0){
       $debtorIDType = "EUN";
       $type = "C";
   }
   
   // Customer Title
   $customer_title="";
   if($customer_row['company_title']==1)
       $customer_title ="Mr.";
   elseif($customer_row['company_title']==2)
       $customer_title ="Mrs.";
   elseif($customer_row['company_title']==3)
       $customer_title = "Ms.";
 
   $address_arr = $CCompany->getAddresses(false,$customer_row['company_id']);
   //print_r($address_arr);
   $address_id = $address_arr[0]['address_id'];
   $address = $address_arr[0]['address_street_address_1']." ".$address_arr[0]['address_street_address_2']." ".$address_arr[0]['address_city']." ".$address_arr[0]['address_state_province']." ".$address_arr[0]['address_postal_zip_code'];
   $contactPerson = "";
   
   // Customer Date
   $company_date ="";
   if($customer_row['company_acceptance_date']!=NULL){
    $company_date = $customer_row['company_acceptance_date'];
    $company_date = date('d/m/Y', strtotime($company_date));
   }
   
   // Customer Country
   $country ="";
   $array_countries5 =$config_type5->getRecordConfigByList('countriesList');
   $country_id = $address_arr[0]['address_country'];
   if($country_id!=0)
    $country = $array_countries5[$country_id]['config_name'];
   
    //Customer ContactNo 1,ContactNo 2,ContactNo 3,ContactNo 4
    $contactNo1="";$contactNo2="";$contactNo3="";$contactNo4="";
    $email="";
    if($address_arr[0]['address_id']!=""){
            if($address_arr[0]['address_phone_1']!="")
                $contactNo1 = $address_arr[0]['address_phone_1'];
            else if($address_arr[0]['address_phone_2']!="")
                $contactNo1 = $address_arr[0]['address_phone_2'];
            else {     
                $contact_arr = $CCompany->getContactInAddress($address_arr[0]['address_id']);
                foreach ($contact_arr as $contact_row) {
                    if($contact_row['contact_mobile']!=""){
                        $contactNo1 = $contact_row['contact_mobile'];
                        break;
                    }
                }
            }
            if($address_arr[0]['address_email']!="")
                $email = $address_arr[0]['address_email'];
            else if($address_arr[0]['address_email_2']!="")
                $email = $address_arr[0]['address_email_2'];
            else {     
                $contact_arr = $CCompany->getContactInAddress($address_arr[0]['address_id']);
                foreach ($contact_arr as $contact_row) {
                    if($contact_row['contact_email']!=""){
                        $email = $contact_row['contact_email'];
                        break;
                    }
                }
            }
            
    }
    
    if($address_arr[1]['address_id']!=""){
        if($address_arr[1]['address_phone_1']!="")
            $contactNo2 = $address_arr[1]['address_phone_1'];
        else if($address_arr[1]['address_phone_2']!="")
            $contactNo2 = $address_arr[1]['address_phone_2'];
        else {     
            $contact_arr1 = $CCompany->getContactInAddress($address_arr[1]['address_id']);
            foreach ($contact_arr1 as $contact_row1) {
                if($contact_row1['contact_mobile']!=""){
                    $contactNo2 = $contact_row1['contact_mobile'];
                    break;
                }
            }
        }
    }
    
    
    if($address_arr[2]['address_id']!=""){
        if($address_arr[2]['address_phone_1']!="")
            $contactNo3 = $address_arr[2]['address_phone_1'];
        else if($address_arr[2]['address_phone_2']!="")
            $contactNo3 = $address_arr[2]['address_phone_2'];
        else {     
            $contact_arr2 = $CCompany->getContactInAddress($address_arr[2]['address_id']);
            foreach ($contact_arr2 as $contact_row2) {
                if($contact_row2['contact_mobile']!=""){
                    $contactNo3 = $contact_row2['contact_mobile'];
                    break;
                }
            }
        }
    }
    
    if($address_arr[3]['address_id']!=""){
        if($address_arr[3]['address_phone_1']!="")
            $contactNo4 = $address_arr[3]['address_phone_1'];
        else if($address_arr[3]['address_phone_2']!="")
            $contactNo4 = $address_arr[3]['address_phone_2'];
        else {     
            $contact_arr3 = $CCompany->getContactInAddress($address_arr[3]['address_id']);
            foreach ($contact_arr3 as $contact_row3) {
                if($contact_row3['contact_mobile']!=""){
                    $contactNo4 = $contact_row3['contact_mobile'];
                    break;
                }
            }
        }
    }
    
    // Total Invoice Customer
    $invoice_arr = $CInvoiceManager->list_invoice($customer_row['company_id']);
    $Amount=0;$amount1=0;$amount2=0;$amount3=0;$amount4=0;$amount5=0;$max_term=0;
    foreach ($invoice_arr as $invoice_row) {
        $invoice_id = $invoice_row['invoice_id'];
        $invoice_revision_id = $CInvoiceManager->get_invoice_revision_lastest($invoice_id);
        $total_show=$CInvoiceManager->get_invoice_item_total($invoice_id, $invoice_revision_id);
        //echo $total_show;
        $total_invoice=$CInvoiceManager->get_total_tax_and_paid($invoice_revision_id, $total_show);
        $Amount += $total_invoice;
        
        $start =  strtotime($invoice_row['invoice_date']);
        $end = strtotime(date("Y/m/d"));
        $day_diff=  round(($end - $start)/86400);
        if($day_diff>=0 && $day_diff<=30){
            $day30=$total_invoice;
            $day60=0;$day90=0;$day=0;$day120=0;
            $amount1 += $day30;
        }
        else if($day_diff>30 && $day_diff<=60)
        {
            $day60=$total_invoice;
            $day30=0;$day90=0;$day=0;$day120=0;
            $amount2 +=$day60;
        }
        else if($day_diff>60 && $day_diff<=90)
        {
            $day90=$total_invoice;
            $day30=0;$day60=0;$day=0;$day120=0;
            $amount3 +=$day90;
        }
        else if( $day_diff>90 && $day_diff <=120)
        {
            $day120=$total_invoice;
            $day30=0;$day60=0;$day90=0;$day=0;
            $amount4 +=$day120;
        }
        else if( $day_diff>120)
        {
            $day=$total_invoice;
            $day30=0;$day60=0;$day90=0;$day120=0;
            $amount5 +=$day;
        }
        
        $term = $invoice_row['term'];
        $term_value=$term_err[$term];
        if($term_value>$max_term && is_numeric($term_value))
            $max_term = $term_value;
        
    }
    
    // Total Customer chua tra het tien nhung chua het han
    $invoice_arr0 = $CInvoiceManager->get_invoice_recAndPartial($customer_row['company_id']);
    $amount0 = 0;
    foreach ($invoice_arr0 as $invoice_row0) {
        $invoice_id0 = $invoice_row0['invoice_id'];
        $invoice_revision_id0 = $CInvoiceManager->get_invoice_revision_lastest($invoice_id0);
        $total_show0=$CInvoiceManager->get_invoice_item_total($invoice_id0, $invoice_revision_id0);
        //echo $total_show;
        $total_invoice0=$CInvoiceManager->get_total_tax_and_paid($invoice_revision_id0, $total_show0);
        $amount0 += $total_invoice0;
    }
    
    //Lay contact dau tien cua customer
    $contactPerson ="";
    $contact_cus_arr = $CInvoiceManager->get_contactCustomer_by_company($customer_row['company_id']);
    $contactPerson=$contact_cus_arr[0]['contact_last_name']." ".$contact_cus_arr[0]['contact_first_name'];
   
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $customer_row['company_id'])
            ->setCellValue('B'.$i, $customer_row['company_id'])
            ->setCellValue('C'.$i, $customer_row['company_rcb'])
            ->setCellValue('D'.$i, $debtorIDType)
            ->setCellValue('E'.$i, $customer_row['company_name'])
            ->setCellValue('F'.$i, $company_date)
            ->setCellValue('G'.$i, $type)
            ->setCellValue('H'.$i, $customer_title)
            ->setCellValue('Q'.$i, $contactPerson)
            ->setCellValue('T'.$i, $address)
            ->setCellValue('U'.$i, $country)
            ->setCellValue('V'.$i, $contactNo1)
            ->setCellValue('W'.$i, $contactNo2)
            ->setCellValue('X'.$i, $contactNo3)
            ->setCellValue('Y'.$i, $contactNo4)
            ->setCellValue('Z'.$i, $email)
            ->setCellValue('AA'.$i, 'SGD')
            ->setCellValue('AB'.$i, $Amount)
            ->setCellValue('AC'.$i, $amount0)
            ->setCellValue('AD'.$i, $amount1)
            ->setCellValue('AE'.$i, $amount2)
            ->setCellValue('AF'.$i, $amount3)
            ->setCellValue('AG'.$i, $amount4)
            ->setCellValue('AH'.$i, $amount5)
            ->setCellValue('AI'.$i, '0')
            ->setCellValue('AJ'.$i, $max_term)
            ->setCellValue('AK'.$i, 0)
            ->setCellValue('AL'.$i, 0)
            ->setCellValue('AM'.$i, 0)
            ->setCellValue('AN'.$i, 0)
            ->setCellValue('AT'.$i, 0)
            ;
//            ->setCellValue('B'.$i, 'CustomerRef')
//            ->setCellValue('C'.$i, 'DebtorID')
//            ->setCellValue('D'.$i, 'DebtorIDType')
//            ->setCellValue('E'.$i, 'Name')
//            ->setCellValue('F'.$i, 'AcctOpenDate')
//            ->setCellValue('G'.$i, 'Type')
//            ->setCellValue('H'.$i, 'Salute')
//            ->setCellValue('I'.$i, 'Gender')
//            ->setCellValue('J'.$i, 'DOB')
//            ->setCellValue('K'.$i, 'MaritalStatus')
//            ->setCellValue('L'.$i, 'Nationality')
//            ->setCellValue('M'.$i, 'Race')
//            ->setCellValue('N'.$i, 'LastEmployer')
//            ->setCellValue('O'.$i, 'PrevEmployer')
//            ->setCellValue('P'.$i, 'Occupation')
//            ->setCellValue('Q'.$i, 'ContactPerson')
//            ->setCellValue('R'.$i, 'Department')
//            ->setCellValue('S'.$i, 'Annual Income')
//            ->setCellValue('T'.$i, 'Address')
//            ->setCellValue('U'.$i, 'Country')
//            ->setCellValue('V'.$i, 'ContactNo 1')
//            ->setCellValue('W'.$i, 'ContactNo 2')
//            ->setCellValue('X'.$i, 'ContactNo 3')
//            ->setCellValue('Y'.$i, 'ContactNo 4')
//            ->setCellValue('Z'.$i, 'Email')
//                    ->setCellValue('AA', 'Currency')
//            ->setCellValue('AB'.$i, 'AmountDue')
//            ->setCellValue('AC'.$i, 'Amount0')
//            ->setCellValue('AD'.$i, 'Amount1')
//            ->setCellValue('AE'.$i, 'Amount2')
//            ->setCellValue('AF'.$i, 'Amount3')
//            ->setCellValue('AG'.$i, 'Amount4')
//            ->setCellValue('AI'.$i, 'Amount6')
//            ->setCellValue('AJ'.$i, 'Credit / Payment Term')
//            ->setCellValue('AK'.$i, 'CreditLimit')
//            ->setCellValue('AL'.$i, 'LoanAmt')
//            ->setCellValue('AM'.$i, 'LoanOSAmt')
//            ->setCellValue('AN'.$i, 'InstalmtAmt')
//            ->setCellValue('AO'.$i, 'Obligation')
//            ->setCellValue('AP'.$i, 'Vehicle No.')
//            ->setCellValue('AQ'.$i, 'Year of Reg/Incorp')
//            ->setCellValue('AR'.$i, 'Principal Activities')
//            ->setCellValue('AS'.$i, 'Capital Structure')
//            ->setCellValue('AT'.$i, 'Charge Details')
//            ->setCellValue('AU'.$i, 'Shareholder Details')
//            ->setCellValue('AV'.$i, 'FORMTITLE')
//            ->setCellValue('AW', 'Records with')//cHU Y
//            ->setCellValue('AX', 'INVNUMBER')
//            ->setCellValue('AY', 'CUSTNUMBER')
//            ->setCellValue('AZ', 'STFIRSTNM')
//                    ->setCellValue('BA', 'STLASTNM')
//            ->setCellValue('BB', 'STCOMPANY')
//            ->setCellValue('BC', 'STADDRESS1')
//            ->setCellValue('BD', 'STADDRESS2')
//            ->setCellValue('BE', 'STCITY')
//            ->setCellValue('BF', 'STSTATE')
//            ->setCellValue('BG', 'STZIP')
//            ->setCellValue('BH', 'STCOUNTRY')
//            ->setCellValue('BI', 'CUSTDISCOU')
//            ->setCellValue('BJ', 'GICOL1CON')
//            ->setCellValue('BK', 'GICOL3CON')
//            ->setCellValue('BL', 'GICOL4CON')
//            ->setCellValue('BM', 'GICOL5CON')
//            ->setCellValue('BN', 'GICOL6CON')
//            ->setCellValue('BO', 'TAX1RATE')
//            ->setCellValue('BP', 'TAX2RATE')
//            ->setCellValue('BQ', 'SUBTOTAL')
//            ->setCellValue('BR', 'TAXTOTAL1')
//            ->setCellValue('BS', 'TAXTOTAL2')
//            ->setCellValue('BT', 'TAX1AMOUNT')
//            ->setCellValue('BU', 'TAX2AMOUNT')
//            ->setCellValue('BV', 'TOTAL')
//            ->setCellValue('BW', 'AMOUNTPAID')
//            ->setCellValue('BX', 'INVCREDLIM')
//            ->setCellValue('BY', 'INVPRINTED')
//            ->setCellValue('BZ', 'DATPRINTED')
//                    ->setCellValue('CA', 'Records with')//cHU Y
//            ->setCellValue('CB', 'DATVOIDED')
//            ->setCellValue('CC', 'INVPAID')
//            ->setCellValue('CD', 'DATPAID')
//            ->setCellValue('CE', 'Records with')//cHU Y
//            ->setCellValue('CF', 'DATDELETED')
//            ->setCellValue('CG', 'MEMO')
//            ->setCellValue('CH', 'FORMKEY')
//            ->setCellValue('CI', 'TAXNAME1')
//            ->setCellValue('CJ', 'TAXNAME2')
//            ->setCellValue('CK', 'INVEMAILED')
//            ->setCellValue('CL', 'DATEMAILED')
//            ->setCellValue('CM', 'EXTRA1')
//            ->setCellValue('CN', 'EXTRA2')
//            ->setCellValue('CO', 'EXTRA3')
//            ->setCellValue('CP', 'EXTRA4')
//            ->setCellValue('CQ', 'EXTRA5')
//            ->setCellValue('CR', 'SHIPCOST')
//            ->setCellValue('CS', 'OVERDUEDAY')
//            ->setCellValue('CT', 'LATECHARGE')
//            ->setCellValue('CU', 'INTERRATE')
//            ->setCellValue('CV', 'RELAFIELD')
//            ->setCellValue('CW', 'RELACREDAT');

}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('DP Info');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="DP_info.xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
