<?php
global $AppUI;

require_once (DP_BASE_DIR."/modules/sales/CSalesManager.php");
require_once (DP_BASE_DIR."/modules/sales/CQuotationManager.php");
require_once(DP_BASE_DIR . '/modules/departments/CDepartment.php');
require_once(DP_BASE_DIR . '/includes/main_functions.php');

$CQuotationManager = new CQuotationManager();
$CSalesManager = new CSalesManager();
$CDepManager = new CDepartment();
$quotation_stt = dPgetSysVal('QuotationStatus');


	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */

        /*
         * Tham so truyen vao 
         */
        $status_id = 0;
        $customer_id = null;
        $quotation_no=false;
        $dep_id = -1;
        if(isset($_GET['status_id']))
            $status_id = $_GET['status_id'];
        if(isset($_POST['customer_id']))
            $customer_id = $_GET['customer_id'];
        if(isset($_GET['quotation_no']))
            $quotation_no = $_GET['quotation_no'];
        
        if(isset($_GET['department_id']))
            $dep_id = $_GET['department_id'];
        
        
	
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "quotation_id";
	
	/* DB table to use */
	$sTable = "sales_quotation";
        $tblAdd = "addresses";
        
	/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */
    
	$aColumns = array( $sTable.'.quotation_no', $sTable.'.quotation_date', $sTable.'.quotation_subject', $sTable.'.customer_id', $sTable.'.user_id', 'clients.company_name',
                $sTable.'.quotation_status',$sTable.'.quotation_id',$sTable.'.job_location_id',
                $tblAdd.'.address_branch_name',$tblAdd.'.address_street_address_2',$tblAdd.'.address_postal_zip_code',$tblAdd.'.address_street_address_1'
            
            );
	$aColumns_search = array( $sTable.'.quotation_no', $sTable.'.quotation_subject', 'clients.company_name',
                $sTable.'.quotation_status',
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
        $where = "";
        if($customer_id != '' && $customer_id != null && $customer_id)
            $where .= ' AND '.$sTable.'.customer_id = '.intval($customer_id);
        $where .= $sTable.'.department_id = '.  intval($dep_id);
        
        if($status_id != 5 && ($status_id != NULL || $status_id ==""))
            $where .= ' AND '.$sTable.'.quotation_status = '.  intval($status_id);

        if($quotation_no)
            $where .= ' AND '.$sTable.'.quotation_no LIKE "%'.$quotation_no.'%"';
        
        if(dPisListOwnSalesPerson()){
            $assignCustomer = "0";
            if(count(dPGetAssignCustomer ())>0)
                $assignCustomer = implode (',', dPGetAssignCustomer());
            $where.=' AND customer_id IN ('.$assignCustomer.')';
        }
        
        
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
            $quotation_id = $aRow['quotation_id'];
            $quotation_revision_id = $CQuotationManager->get_quotation_revision_lastest($quotation_id);
            
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
//            }
                $load_quotation = '<span onmouseover="load_quotation_rev('.$quotation_id.'); return false;" onclick="quotation_rev_more('.$quotation_id.');" id="quotation-rev-more-'.$quotation_id.'" class="quotation-rev-more" title="Quotation rev more..">[+]</span>
                        <a href="?m=sales&show_all=1&tab=0&status=update&quotation='.$quotation_id.'&quotation_revision_id='.$quotation_revision_id.'" onclick="load_quotation('.$quotation_id.', '. $quotation_revision_id .', \'update\'); return false;">'. $aRow['quotation_no'].'</a>';
                    
            $row = array(
                    '<input type="checkbox" id="check_list_quo" name="check_list_quo" value="'.$quotation_id.'">
                    <input type="hidden" id="quotation_status_'.$quotation_id.'" name="quotation_status_'.$quotation_id.'" value="'.$aRow['quotation_status'].'" >',
                    $load_quotation .'<div class="div-quotation-rev" id="div-quotation-rev-'.$quotation_id.'" style="display: none"></div>    ',
                    '<div class="client_quotation" style="font-weight:bold;" id="'.$quotation_id.'">'.$aRow['company_name'].'.</div>
                    <div style="font-size:0.9em;color:#666;">'.$job_location.'</div>',
                    $CSalesManager->htmlChars($aRow['quotation_subject']),
                    date($ocio_config['php_abms_date_format'],  strtotime($aRow['quotation_date'])),
                    $total_item_show = '$'.number_format(round($CQuotationManager->get_total_tax_and_paid($quotation_revision_id, $CQuotationManager->get_quotation_item_total($quotation_id, $quotation_revision_id)),2),2),
                    '<span id="'.$quotation_id.'" class="quotation_status">'.$quotation_stt[$aRow['quotation_status']].'</span>',
            );
            $row['DT_RowId']=$quotation_id;

		$output['aaData'][] = $row;
	}
	
	echo json_encode( $output );
        
?>