<?php
if (!defined('ABMS_CRON')) die('You should not access this file directly.');
class zipfile
{
    
    function Zip($source, $destination, $include_dir = false)
    {

        if (!extension_loaded('zip') || !file_exists($source)) {
            return false;
        }

        if (file_exists($destination)) {
            unlink ($destination);
        }

        $zip = new ZipArchive();
        $open = $zip->open($destination, ZIPARCHIVE::CREATE);
        if (!$open) {
            return false;
        }
        $source = str_replace('\\', '/', realpath($source));

        if (is_dir($source) === true)
        {

            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

            if ($include_dir) {

                $arr = explode("/",$source);
                $maindir = $arr[count($arr)- 1];

                $source = "";
                for ($i=0; $i < count($arr) - 1; $i++) { 
                    $source .= '/' . $arr[$i];
                }

                $source = substr($source, 1);

                $zip->addEmptyDir($maindir);

            }

            foreach ($files as $file)
            {
                $file = str_replace('\\', '/', $file);

                // Ignore "." and ".." folders
                if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                    continue;

                $file = realpath($file);

                if (is_dir($file) === true)
                {
                    $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                }
                else if (is_file($file) === true)
                {
                    $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                }
            }
        }
        else if (is_file($source) === true)
        {
            $zip->addFromString(basename($source), file_get_contents($source));
        }

        return $zip->close();
    }
}
function sales_cron_saleBackupInvoices()
{
    require_once (DP_BASE_DIR."/modules/sales/CInvoiceManager.php");
    $CInvoiceManager = new CInvoiceManager();

    $dir_invoice = DP_BASE_DIR.'/backup/sales_pdf/invoices';
    
    // Dem forder trong thu muc chi dinh de kiem tra lan chay cron dau tien
    $files_invoice = array_diff(scandir($dir_invoice), array('..', '.'));
     
   // Cron save invoice
    if(count($files_invoice)<1)
    {
        $invoice_arr = $CInvoiceManager->get_db_list_invoice();
        foreach ($invoice_arr as $invoice_row) {
            $invoice_id = $invoice_row['invoice_id'];
            $invoice_rev_id = $CInvoiceManager->get_invoice_revision_lastest($invoice_id);
            $CInvoiceManager->create_invoice_cron_pdf_file($invoice_id, $invoice_rev_id, $dir_invoice);
        }
    }
    else // Luu cac invoice update, add trong ngay.
    {
        $invoice_arr = $CInvoiceManager->get_invoice_history_today();
        foreach ($invoice_arr as $invoice_row) {
            $invoice_id = $invoice_row['quo_invc_id'];
            $invoice_rev_id = $CInvoiceManager->get_invoice_revision_lastest($invoice_id);
            $CInvoiceManager->create_invoice_cron_pdf_file($invoice_id, $invoice_rev_id, $dir_invoice);
        }
    }
    
}
function sales_cron_salesBackupQuotations()
{
    require_once (DP_BASE_DIR."/modules/sales/CQuotationManager.php");
    $CQuotationManager = new CQuotationManager();
    
    $dir_quotation = DP_BASE_DIR.'/backup/sales_pdf/quotations';
    $files_quotation = array_diff(scandir($dir_quotation), array('..', '.'));
    // Cron save quotation
    if(count($files_quotation)<1)
    {
        $quotation_arr = $CQuotationManager->get_list_db_quotation();
        foreach ($quotation_arr as $quotation_row) {
            $quotation_id = $quotation_row['quotation_id'];
            $quotation_rev_id = $CQuotationManager->get_quotation_revision_lastest($quotation_id);
            $CQuotationManager->create_quotation_cron_pdf_file($quotation_id, $quotation_rev_id,$dir_quotation);
        }
    }
    else // Luu cac invoice update, add trong ngay.
    {
        $quotation_arr = $CQuotationManager->get_quotation_history_today();
        foreach ($quotation_arr as $quotation_row) {
            $quotation_id = $quotation_row['quo_invc_id'];
            $quotation_rev_id = $CQuotationManager->get_quotation_revision_lastest($quotation_id);
            $CQuotationManager->create_quotation_cron_pdf_file($quotation_id, $quotation_rev_id,$dir_quotation);
        }
    }
}
function sales_cron_saleBackupZip()
{
    $ziper = new zipfile();
    
    $source = "backup/sales_pdf/";
    //echo $source;
    $destination = "backup/zip.zip";
    $ziper->Zip($source, $destination,true);
    
    // connect and login to FTP server SIT
    $ftp_server = "ftp.lionsoftwaresolutions.com";
    $ftp_username ="lsssit";
    $ftp_userpass ="zthfTk7x7jO";
    
    // connect and login to FTP server UAT
    if (is_file( DP_BASE_DIR . '/includes/cron_config.php')) {
        require_once DP_BASE_DIR . '/includes/cron_config.php';
    }
        
    $ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
    $login = ftp_login($ftp_conn, $ftp_username, $ftp_userpass);

    $file = "backup/zip.zip";
    chmod($file, 0777);

    // upload file
    if(ftp_put($ftp_conn, "backup/sales_backup_".date('YmdH').".zip", $file, FTP_BINARY))
    {
            unlink($file);
    }

    // close connection
    ftp_close($ftp_conn);
   
}

function sales_cron_agingreport()
{
    require_once (DP_BASE_DIR."/modules/sales/CReportManager.php");
    require_once (DP_BASE_DIR."/modules/sales/CInvoiceManager.php");
    
    $dir_quotation = DP_BASE_DIR.'/backup/sales_pdf/aging_report';
    $files_quotation = array_diff(scandir($dir_quotation), array('..', '.'));
    $reportManage = new CReportManager();
    if(!file_exists($dir_quotation))
    {
        mkdir($dir_quotation, 0777);
    }
    $date = date('Y-m-d');
    $dateTime = date('d/m/Y H:i:s');
    if(isset($_GET['date']))
        $date=$_GET['date'];
    
    if(isset($_GET['date_to']) && isset($_GET['date_from']))
    {
        $date_staer=$_GET['date_from'];
        while(strtotime($date_staer) <=  strtotime($_GET['date_to']))
        {
            
            $reportManage->cron_aging_report_pdf_file($date_staer,$dir_quotation,$dateTime);
            $date_staer=date("Y-m-d", strtotime("$date_staer +1 day"));; 
            
        }
    }
    
    else
    	$reportManage->cron_aging_report_pdf_file($date,$dir_quotation,$dateTime);
}


//Update department cua invoice duoc tao tu quotation accepted
function sales_cron_department_invoice()
{
   
    require_once (DP_BASE_DIR."/modules/sales/CInvoiceManager.php");
    // Lay tat ca cac invoice co quotation
    $invoiceManager = new CInvoiceManager();
    
    $allInvoice = $invoiceManager->getQuotationByInvoice();
    foreach ($allInvoice as $data)
    {
        //update department cua invoice
        $invoice_id = $data['invoice_id'];
        $department_id=$data['department_id'];
        
        $q = new DBQuery();
        $q->addTable('sales_invoice');
        $q->addUpdate('department_id',$department_id);
        $q->addWhere ('invoice_id ='.$invoice_id);
        $rows = $q->loadList();
        
    }
}
?>
