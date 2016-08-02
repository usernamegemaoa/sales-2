<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
require_once (DP_BASE_DIR . '/modules/sales/CContractQuotation.php');
require_once (DP_BASE_DIR . '/modules/engagements/engagementManager.class.php');
$CContractQuotation = new CContractQuotation();
$CContract = new EngagementManager();

$quotation_id = $_POST['quotation_id'];
$contract_quotation_arr = $CContractQuotation->getContractQuotaiton($quotation_id);
$i=0;

    
?>
<div style="text-align: left;">
    <ul>
        <?php
            if(isset($_POST['status_id']) && $_POST['status_id'] > 0){
                echo '<span>This quotation is accepted, can not add more items. Please add items to Variance Orders in Module contract instead. Click one contract</span>';
            }
            foreach ($contract_quotation_arr as $contract_quotation_item) {
            $i++;
            $contract_arr = $CContract->getEngagements($contract_quotation_item['contract_id']);
            if(isset($_POST['status_id']) && $_POST['status_id'] > 0){//&quotation_id="'.$quotation_id.'"
                echo '<li><a style="padding: 3px;" href="?m=engagements&a=view_details&engagement_id='.$contract_arr['engagement_id'].'&tab=0&quotation_id='.$quotation_id.'">'.$i.'. '.$contract_arr['engagement_code'].' - '.date('d M Y',  strtotime($contract_arr['engagement_start_date'])).' to '.date('d M Y',  strtotime($contract_arr['engagement_end_date'])).'</a></li>';
            }
            else
                echo '<li><a style="padding: 3px;" href="?m=engagements&a=view_details&engagement_id='.$contract_arr['engagement_id'].'&tab=0">'.$i.'. '.$contract_arr['engagement_code'].' - '.date('d M Y',  strtotime($contract_arr['engagement_start_date'])).' to '.date('d M Y',  strtotime($contract_arr['engagement_end_date'])).'</a></li>';

        } ?>
    </ul>
</div>
