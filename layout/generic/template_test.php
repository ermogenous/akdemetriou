<?php
function template_header()
{
    //template used
    //https://blackrockdigital.github.io/startbootstrap-sb-admin-2/pages/index.html
    global $main, $db;
    $db->enable_jquery();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo $main["conf_title"] . " " . $db->admin_title; ?></title>
        <LINK REL="SHORTCUT ICON" HREF="<?php echo $main["site_url"]; ?>/favicon.png">
        <link rel="stylesheet" href="<?php echo $db->admin_layout_url; ?>style.css" rel="stylesheet">

        <link rel="stylesheet" href="<?php echo $main["site_url"]; ?>/scripts/bootstrap-4/css/bootstrap.min.css"
              rel="stylesheet" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
              crossorigin="anonymous">
        <script defer src="https://use.fontawesome.com/releases/v5.0.10/js/all.js"
                integrity="sha384-slN8GvtUJGnv6ca26v8EzVaR9DC58QEwsIk9q1QXdCU8Yu8ck/tL/5szYlBbqmS+"
                crossorigin="anonymous"></script>

        <?php echo $db->admin_more_head;?>
    </head>
    <body>
    <?php if ($db->admin_layout_printer != 'yes') { ?>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="<?php echo $main["site_url"]; ?>/home.php"><i class="fas fa-home"></i>
                        Home <span class="sr-only">(current)</span></a>
                </li>
                <?php
                if ($_SESSION[$main["environment"] . "_admin_username"] == "") {
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-sign-in-alt"></i> Login</a>
                    </li>
                    <?php
                }
                if ($_SESSION[$main["environment"] . "_admin_username"] != "") {
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $main["site_url"]; ?>/login.php?action=logout"><i
                                    class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fab fa-quora"></i> Quotations
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item"
                               href="<?php echo $main["site_url"]; ?>/quotations/quotations.php"><i
                                        class="fas fa-eye"></i> View My Quotations</a>
                            <a class="dropdown-item"
                               href="<?php echo $main["site_url"]; ?>/quotations/quotations_modify.php"><i
                                        class="fas fa-plus-circle"></i> New Quotation</a>
                            <div class="dropdown-divider"></div>
                            <?php if ($db->user_data['usg_approvals'] == 'ANSWER' || $db->user_data['usr_user_rights'] == 0) { ?>
                                <a class="dropdown-item"
                                   href="<?php echo $main["site_url"]; ?>/quotations/approvals.php"><i
                                            class="fas fa-eye"></i>Approvals</a>
                            <?php } ?>
                        </div>
                    </li>
                    <!-- USERS -->
                    <?php if ($db->user_data["usr_user_rights"] == 0) { ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-users"></i> Users
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="<?php echo $main["site_url"]; ?>/users/users.php"><i
                                            class="fas fa-eye"></i> View Users</a>
                                <a class="dropdown-item" href="<?php echo $main["site_url"]; ?>/users/users_modify.php"><i
                                            class="fas fa-plus-circle"></i> New User</a>
                                <?php if ($db->user_data["usr_user_rights"] == 0) { ?>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="<?php echo $main["site_url"]; ?>/users/groups.php"><i
                                                class="fas fa-eye"></i> View Groups</a>
                                    <a class="dropdown-item"
                                       href="<?php echo $main["site_url"]; ?>/users/groups_modify.php"><i
                                                class="fas fa-plus-circle"></i> New Group</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item"
                                       href="<?php echo $main["site_url"]; ?>/users/permissions.php"><i
                                                class="fas fa-eye"></i> View Permissions</a>
                                    <a class="dropdown-item"
                                       href="<?php echo $main["site_url"]; ?>/users/permissions_modify.php"><i
                                                class="fas fa-plus-circle"></i> New Permission</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="<?php echo $main["site_url"]; ?>/users/codes.php"><i
                                                class="fas fa-eye"></i> View Codes</a>
                                    <a class="dropdown-item"
                                       href="<?php echo $main["site_url"]; ?>/users/codes_modify.php"><i
                                                class="fas fa-plus-circle"></i> New Code</a>
                                <?php } ?>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $main["site_url"]; ?>/pricing/pricing.php"><i
                                        class="fas fa-hand-holding-usd"></i> Pricing</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-cogs"></i>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item"
                                   href="<?php echo $main["site_url"]; ?>/send_auto_emails/send_auto_emails.php">
                                    <i class="fas fa-envelope"></i> Auto Emails</a>
                            </div>
                        </li>
                    <?php } ?>


                <?php } ?>

                <!--
                            <li class="nav-item">
                                <a class="nav-link disabled" href="#">Disabled</a>
                            </li>
                 -->
            </ul>

        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-1"></div>
            <div class="col-11"><img src="<?php echo $db->admin_layout_url; ?>/images/logo.png"></div>
        </div>
    </div>

    <br>
    <?php
    if ($_GET['alert-success'] != '') {
        ?>

        <br>
        <div class="container">
            <div class="row">
                <div class="col-3"></div>
                <div class="alert alert-success alert-dismissible fade show col-6" role="alert">
                    <?php echo $_GET["alert-success"]; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="col-3"></div>
            </div>
        </div>
        <?php
    }


}//printer layout
}//template_header
function template_footer()
{
    global $db, $main;

if ($db->admin_layout_printer != 'yes')
{

    ?>
<br><br>
    <div class="container-fluid">
        <div class="row">&nbsp</div>
        <div class="row glyphicon-copyright-mark navbar-custom">&nbsp</div>
    </div>
<?php
}//printer layout
?>

    <script src="<?php echo $main["site_url"]; ?>/scripts/bootstrap-4/js/popper.min.js"></script>
    <script src="<?php echo $main["site_url"]; ?>/scripts/bootstrap-4/js/bootstrap.min.js"></script>
    </body>
    </html>
    <?php
}//template_footer
?>