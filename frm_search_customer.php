<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
?>
<div id="frm_search_customer">
    <table>
        <tr>
            <td width="20%">Address:</td>
            <td>
                <input type="text" class="text" size="33" onkeyup="lookupClient(this.value)" name="txt_key_address" id="txt_key_address" />&nbsp;&nbsp;
<!--                <button name="btt_search" id="btt_search" class="ui-button ui-state-default ui-corner-all" >Search</button>-->
            </td>
       </tr>
       <tr>
           <td colspan="2" height="20">
               <div id="client_suggestions">
                   <div id="autoClientSuggestionsList"></div>
               </div>
           </td>
       </tr>
       <tr align="center">
           <td colspan="2">
               <input type="button" class="ui-button ui-state-default ui-corner-all" name="apply_customer" onclick="load_apply_customer();return false;" id="apply_customer" value="Apply" />
               <input type="button" class="ui-button ui-state-default ui-corner-all" name="cancel_customer" onclick="closeDialogPopup('div-popup')" id="cancel_customer" value="Cancel" />
           </td>
       </tr>
    </table>
</div>