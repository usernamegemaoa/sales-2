<?php


    
global $AppUI;
require_once (DP_BASE_DIR."/modules/sales/CSalesManager.php");
require_once (DP_BASE_DIR."/modules/sales/CInvoiceManager.php");
require_once (DP_BASE_DIR."/modules/sales/CCreditNoteManager.php");

$CSalesManager = new CSalesManager();
$CInvoiceManager = new CInvoiceManager();
$CCreditManager = new CCreditNoteManager();
$invoice_stt = dPgetSysVal('InvoiceStatus');

	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */

        /*
         * Tham so truyen vao 
         */
         $status_id = false;
        if(isset($_GET['status_id']))
            $status_id = $_GET['status_id'];
        $customer_id=false;
        if(isset($_GET['customer_id']))    
            $customer_id = $_GET['customer_id'];
        $invoice_no = false;
        if(isset($_GET['invoice_no']))
            $invoice_no = $_GET['invoice_no'];
        
        $contract_no=false;
        if(isset($_GET['contract_no']) && $_GET['contract_no']!='null')
            $contract_no = $_GET['contract_no'];
        
        
	
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "invoice_id";
	
	/* DB table to use */
	$sTable = "sales_invoice";
        $tblAdd = "addresses";
	/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */
	$aColumns = array( $sTable.'.invoice_no', $sTable.'.invoice_date', $sTable.'.invoice_subject', $sTable.'.customer_id', $sTable.'.user_id', 'clients.company_name',
                $sTable.'.invoice_status',$sTable.'.invoice_id',$sTable.'.job_location_id'
            ,                $tblAdd.'.address_branch_name',$tblAdd.'.address_street_address_2',$tblAdd.'.address_postal_zip_code',$tblAdd.'.address_street_address_1'

            );
	$aColumns_search = array( $sTable.'.invoice_no',  $sTable.'.invoice_subject', 'clients.company_name',
              
                         $tblAdd.'.address_branch_name',$tblAdd.'.address_street_address_2',$tblAdd.'.address_postal_zip_code',$tblAdd.'.address_street_address_1'

            );
        
	
	/* Database connection information */
	$gaSql['user']       = $ocio_config['dbuser'];
	$gaSql['password']   = $ocio_config['dbpass'];
	$gaSql['db']         = $ocio_config['dbname'];
	$gaSql['server']     = $ocio_config['dbhost'];
	
	/* REMOVE THIS LINE (it just includes my SQL connection user/pass) */
	//include( $_SERVER['DOCUMENT_ROOT']."/datatables/mysql.php" );
	
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
	 * no need to edit below this line
	 */
	
	/* 
	 * MySQL connection
	 */
	$gaSql['link'] =  mysql_pconnect( $gaSql['server'], $gaSql['user'], $gaSql['password']  ) or
		die( 'Could not open connection to server' );
	
	mysql_select_db( $gaSql['db'], $gaSql['link'] ) or 
		die( 'Could not select database '. $gaSql['db'] );
        
        
        
        /*############ THONGNV #################*/
        
        /*
         * Dieu kien loc
         */
        $where_arr = array();
        if($customer_id != '' && $customer_id != null && $customer_id)
            $where_arr[]= ' '.$sTable.'.customer_id = '.intval($customer_id);
        if($status_id==4)
            $where_arr[]= $sTable.'.invoice_status <> 2 ';
        else if(($status_id==0 || $status_id) && $status_id!="")
            $where_arr[] = $sTable.'.invoice_status = '.  intval($status_id);
        if($contract_no)
        {
            require_once (DP_BASE_DIR . '/modules/sales/CContractInvoice.php');
            $CContractInvoice = new CContractInvoice();
            $contract_invoice_arr = $CContractInvoice->getInvoiceContract($contract_no);
            $invoice_id_arr = array(0=>'0');
            foreach ($contract_invoice_arr as $contract_invoice_item) {
                $invoice_id_arr[]= $contract_invoice_item['invoice_id'];
            }

            $where_arr[]= ' '.$sTable.'.invoice_id IN ('.  implode(",", $invoice_id_arr).')';
        }
        if($invoice_no)
            $where_arr[]= ' '.$sTable.'.invoice_no LIKE "%'.$invoice_no.'%"';
        $where = implode(' AND ', $where_arr);
        /*
         * JOIN TABLE
         */
        $join = "LEFT JOIN clients ON $sTable.customer_id=clients.company_id ";
        $join .= "LEFT JOIN addresses ON $sTable.job_location_id=addresses.address_id ";
        /**************** END THONG NV ******************/
	
	
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_GET['iDisplayLength'] );
	}
	
	
	/*
	 * Ordering
	 */
	$sOrder = "";
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
				 	".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}
	
	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	$sWhere = "";
	if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
	{
		$sWhere = "WHERE (";
		for ( $i=0 ; $i<count($aColumns_search) ; $i++ )
		{
			$sWhere .= $aColumns_search[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns_search) ; $i++ )
	{
		if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$sWhere .= $aColumns_search[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
		}
	}
        
        if($where!="")
        {
            if ( $sWhere == "" )
            {
                    $sWhere = "WHERE ";
            }
            else
            {
                    $sWhere .= " AND ";
            }
            $sWhere .= $where;
        }
            
	
	
	/*
	 * SQL queries
	 * Get data to display
	 */
	$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
                $join
		$sWhere
		$sOrder
		$sLimit
	";
	$rResult = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	
	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	$rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM   $sTable
	";
        if($where!="")
            $sQuery.= 'WHERE '.$where;
	$rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	$aResultTotal = mysql_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
	
	
	/*
	 * Output
	 */
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
        
	while ( $aRow = mysql_fetch_array( $rResult ) )
	{
            $invoice_id = 0;
            $invoice_id = $aRow['invoice_id'];
            $invoice_revision_id = $CInvoiceManager->get_invoice_revision_lastest($invoice_id);
            $total_show=$CInvoiceManager->get_invoice_item_total($invoice_id, $invoice_revision_id);
            
              // get joblocation
            // get joblocation
            $brand="";
            if($aRow['address_branch_name']!="")
                $brand=$aRow['address_branch_name'].' - ';
            $address_2 ="";
            if($aRow['address_street_address_2']!="")
                $address_2= ', '.$aRow['address_street_address_2'];
            $postal_code_job = '';
            if($aRow['address_postal_zip_code']!="")
                $postal_code_job .=', Singapore '.$aRow['address_postal_zip_code'];
            $job_location = "";
//            if($job_location_arr!=""){
                    $job_location.=$brand.$CSalesManager->htmlChars($aRow['address_street_address_1'].$address_2.$postal_code_job);    
            
                //Lấy credinote được áp dụng cho invoice//
                $credit_arr = $CCreditManager->getCreditNoteByInvoice($invoice_id);
                $credit_no_arr = array();
                foreach ($credit_arr as $credit_item) {
                    $credit_no_arr[]='<a href="?m=sales&show_all=1&tab=2&creditNote_id='.$credit_item['credit_note_id'].'&status=update">'.$credit_item['credit_note_no'].'</a>';
                }
                    
                    $row = array(
                        
                        '<input type="checkbox" id="check_list" name="check_list" value="'.$invoice_id.'">
                         <input id="invoice_status_'.$invoice_id.'" type="hidden" value="'.$aRow['invoice_status'].'" name="invoice_status_'.$invoice_id.'">
                        ',
                        '<span onmouseover="load_invocie_rev('.$invoice_id.'); return false;" onclick="invoice_rev_more('.$invoice_id.');" id="invoice-rev-more-'.$invoice_id.'" class="invoice-rev-more" title="Invoice rev more..">[+]</span>
                            <a href="?m=sales&show_all=1&tab=1&status_rev=update&invoice_id='.$invoice_id.'&invoice_revision_id='.$invoice_revision_id.'" onclick="load_invoice('. $invoice_id .', '. $invoice_revision_id .', \'update\'); return false;">'. $aRow['invoice_no'].'</a>
                            <!-- <a id="a_view_payment" href="#" style="float: right;" onclick="load_payment('. $invoice_id .', '. $aRow['customer_id'] .'); return false;">View payment</a> --> 
                            <div class="div-invoice-rev" id="div-invoice-rev-'.$invoice_id.'" style="display: none"></div>    ',
                        '<div class="client_invoice" style="font-weight:bold;" id="'.$invoice_id.'">'.$aRow['company_name'].'.</div>
                        <div style="font-size:0.9em;color:#666;">'.$job_location.'</div>',
                        implode(',',$credit_no_arr),
                        $CSalesManager->htmlChars($aRow['invoice_subject']),
                        $CSalesManager->htmlChars($aRow['po_number']),
                        date($ocio_config['php_abms_date_format'],  strtotime($aRow['invoice_date'])),
                        '$'. number_format($CInvoiceManager->get_total_tax_and_paid($invoice_revision_id, $total_show),2),
                        '<span id="'.$invoice_id.'" class="invoice_status">'.$invoice_stt[$aRow['invoice_status']].'</span>'   
                    );
                    $row['DT_RowId']=$invoice_id;

		$output['aaData'][] = $row;
	}
	
	echo json_encode( $output );
        
?>