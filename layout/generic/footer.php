</div><?php
if ($_GET["layout_action"] != "printer") {
?></td>
              </tr>
            </table></td>
          <td width="14" align="left" valign="top" background="<?php echo $db->admin_layout_url;?>images/center_vertical_line.jpg"><br />
          <br /><br /></td>
        </tr>
        <tr>
          <td height="14" colspan="5" background="<?php echo $db->admin_layout_url;?>images/center_back_horizontal_line.jpg"><img src="<?php echo $db->admin_layout_url;?>images/spacer.gif" width="1" height="1" /><img src="<?php echo $db->admin_layout_url;?>images/spacer.gif" width="1" height="1" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td height="28" colspan="2" align="center">
      &copy;&nbsp;Copyright Ydrogios Insurance 2008</td>
  </tr>
</table>
<?php
}
?>
</body>
</html>
<?
$db->main_exit();
?>