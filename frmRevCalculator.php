<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
?>
<div>
    <form>
        <table width="100%">
            <tr>
                <td width="60" style="text-align: right;">Total:</td>
                <td><input type="text" class="text" id="calculator_total" name="calculator_total" value="0.00" /></td>
            </tr>
            <tr>
                <td style="text-align: right;">GST:</td>
                <td>
                    <select name="calculator_tax" id="calculator_tax" style="width:60px;" class="text">
                        <option value="0">--</option>
                        <option value="1" selected>7</option>
                    </select> %
                </td>
            </tr>
            <tr align="center" valign="bottom">
                <td colspan="2" height="35px" align="center">
                    <input type="button" onclick="reverse_calculator(); return false;" class="ui-button ui-state-default ui-corner-all" value="Calculate" >&nbsp;&nbsp;&nbsp;
                    <input type="reset" value="Reset" class="ui-button ui-state-default ui-corner-all" /></td>
            </tr>
        </table>
    <table>
        <tr>
            <td>Tax Total:</td>
            <td><input type="text" name="cal_tax_total" id="cal_tax_total" readonly="true" value="0.00" /></td>
        </tr>
        <tr>
            <td>Sub Total:</td>
            <td><input type="text" name="cal_sub_total" id="cal_sub_total" readonly="true" value="0.00" /></td>
        </tr>
<!--        <tr>
            <td><button class="ui-button ui-state-default ui-corner-all" onclick="">Save Applied</button></td>
            <td><button class="ui-button ui-state-default ui-corner-all" onclick="">Close</button></td>
        </tr>-->
    </table>
        </form>
</div>