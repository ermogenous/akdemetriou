<?php
include("include/main.php");
$db = new Main(1);
$db->show_header();

?>

<div class="container-fluid" style="background-color: #132D52;">

    <div class="row">
        <div class="col-12 home_butterfly_bg" style="height: 473px">
            <div class="container-fluid justify-content-center align-self-center">
                <div class="row" style="height: 173px;"></div>
                <div class="row">
                    <div class="col main_text_big_white">Need a Medical plan for your family members? Look no further!</div>
                    <div class="col" style="font-size: 25px; color: white;">DCare medical plan, designed by A.K. Demetriou Insurance Agents, Sub-agents and Consultants Limited,
                        and Underwritten by certain underwriters at Lloyd’s via DUAL Corporate Risks Limited.</div>
                    <div class="col text-center"><img src="<?php echo $db->admin_layout_url; ?>/images/dcare-logo.png"> </div>
                </div>
            </div>
        </div>
    </div>

</div>

<br />
<noscript>
 For full functionality of this site it is necessary to enable JavaScript.
 Here are the <a href="http://www.enable-javascript.com/" target="_blank">
 instructions how to enable JavaScript in your web browser</a>.
</noscript>
<?php
$db->show_footer();
?>
