<?php
require_once (DP_BASE_DIR."/modules/sales/CCreditNoteManager.php");
require_once(DP_BASE_DIR."/modules/sales/CInvoiceManager.php");
require_once (DP_BASE_DIR."/modules/sales/CSalesManager.php");

    $CCreditManager = new CCreditNoteManager();
    $CSalesManager = new CSalesManager();
    global $ocio_config,$AppUI,$deleteView_creditNote,$canDelete, $accessView_creditNote;
    $invoice_stt = dPgetSysVal('InvoiceStatus');
    $status = $_POST['status'];
    $credit_status =  dPgetSysVal('CreditStatus');
    $sIndexColumn = "credit_note_id";
    $sTable = "sales_credit_note";
    $aColumns = array($sTable.'.credit_note_no,clients.company_name,sales_invoice.invoice_no');
//    
    $gaSql['user']       = $ocio_config['dbuser'];
    $gaSql['password']   = $ocio_config['dbpass'];
    $gaSql['db']         = $ocio_config['dbname'];
    $gaSql['server']     = $ocio_config['dbhost'];
    $gaSql['link'] =  mysql_pconnect( $gaSql['server'], $gaSql['user'], $gaSql['password']  ) or
		die( 'Could not open connection to server' );
	
	mysql_select_db( $gaSql['db'], $gaSql['link'] ) or 
		die( 'Could not select database '. $gaSql['db'] );
        $where_arr = array();

        if($_GET['status'] && $_GET['status']!="")
            $where_arr[]= "credit_note_status =".intval($_GET['status']);
        $where = implode(' AND ', $where_arr);
        $sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_GET['iDisplayLength'] );
	}
        
//	
//	
//	/*
//	 * Ordering
//	 */
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
        $sWhere = "";
	if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
	{
            
		$sWhere = "WHERE (";

		$sWhere .= "sales_credit_note.credit_note_no LIKE '%".mysql_real_escape_string( $_GET['sSearch'])."%' OR "
                        . "clients.company_name LIKE '%".mysql_real_escape_string( $_GET['sSearch'])."%' OR "
                        . "sales_invoice.invoice_no LIKE '%".mysql_real_escape_string( $_GET['sSearch'])."%' OR "
                        . "sales_invoice.invoice_no LIKE '%".mysql_real_escape_string( $_GET['sSearch'])."%' OR "
                       ;

                
		$sWhere = substr_replace( $sWhere, "", -3 );
                
                //Search theo address
                
//                $sWhere.= ' OR clients.company_name  '; 
                
		
		$sWhere .= ')';
                

	}
        
        $join = "LEFT JOIN clients ON $sTable.customer_id=clients.company_id ";
        $join .= "LEFT JOIN sales_invoice ON $sTable.invoice_id=sales_invoice.invoice_id ";
//        $join .= "LEFT JOIN sysvals ON sales_invoice.invoice_status=sysvals.invoice_id ";
//        echo $sWhere;
        
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
            $sQuery = "
		SELECT SQL_CALC_FOUND_ROWS $sTable.*,clients.company_name,sales_invoice.invoice_no
		FROM   $sTable
                $join
		$sWhere
		$sOrder
		$sLimit
	";
//        
	$rResult = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
//	
//	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	$rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
//	
//	/* Total data set length */
	$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM   $sTable
	";
        if($where!="")
            $sQuery.= 'WHERE '.$where;
	$rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	$aResultTotal = mysql_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
//	
//	
//	/*
//	 * Output
//	 */
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
        while ( $creditNote_row = mysql_fetch_array( $rResult ) )
	{
          
        $status = $creditNote_row['credit_note_status'];
        
        $invoice_id = $creditNote_row['invoice_id'];
        $invoice_manage = new CInvoiceManager();
        $invocie = $invoice_manage->get_db_invoice($invoice_id);
        $invoice_revision_id = $invoice_manage->get_invoice_revision_lastest($invoice_id);
        $company_name=$creditNote_row['company_name'];
        if($invoice_id>0)
        {
            $company=$invoice_manage->list_invoice(false, false,false, false,false, false, false,false,$invoice_id);
            $company_name=$company[0]['company_name'];
        }
        $row = array(
            '<input type="checkbox" name="check_list" id="check_list" value="'.$creditNote_row['credit_note_id'].'">',
            '<a href="" onclick="load_creditNote('.$creditNote_row['credit_note_id'].',\'update\'); return false;">'.$creditNote_row['credit_note_no'].'</a>',
            $company_name,
            date($ocio_config['php_abms_date_format'], strtotime($creditNote_row['credit_note_date'])),
            '<a href="?m=sales&show_all=1&tab=1&status_rev=update&invoice_id='.$invoice_id.'&invoice_revision_id='.$invoice_revision_id.'">'.$creditNote_row['invoice_no'].'</a>',
            $invoice_stt[$invocie[0]['invoice_status']],
            '$'.number_format($CSalesManager->total_creditNote_amount_and_tax($creditNote_row['credit_note_id']),2),
            $credit_status[$status]
        );

                $row['DT_RowId']=$creditNote_row['credit_note_id'];;
		$output['aaData'][] = $row;
	}
	
	echo json_encode( $output );