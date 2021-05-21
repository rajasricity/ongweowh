<?php

defined("BASEPATH") OR exit("No direct script access allow");


function admin_header(){
	include APPPATH."views/admin/back_common/header.php";
}

function adminuser_sidebar(){
	include APPPATH."views/admin/back_common/adminsidebar.php";
}

function admin_footer(){
	include APPPATH."views/admin/back_common/footer.php";
}

function admin_sidebar(){
	include APPPATH."views/admin/back_common/sidemenu.php";
}


?>
