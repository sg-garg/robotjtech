<?php
if (md5(domain.'89yoliogp9suiogbnkhjo94u984yu7895t4yi5goi34h5908y34958yihkjfhskdfh98y495uhkjehrkh943y85ihuktrnkwjhfo84987598347508hf,jshdkfhkjh4938y598345hkjhdkfjh983459yijhkf89649786593745jkhkds9f878979345').md5(domain.'8y4ukhih8gfkjnkjnbi88yrti98hykjbdk09ulfj;okdpf0ua098dyfkabkdfnkihioy80489yhiyugu6t8T8yt*O&T&*^TGKJUTO*&RT&UFUKYTG*(YU{))I("POJKJHJ<HGUK&T*I&TTURDFJHo;iy98676467587yluhjkugy8it8igjklgil8y9oli')!=serialnumber)
exit('Invalid Serial Number');

$cronjobs[]=array('classname'=>'cc_admin_clean_paidmail');

class cc_admin_clean_paidmail {

var $class_name='cc_admin_clean_paidmail';
var $minutes=60;

function cronjob(){

    $getrow=cronjob_query("select * from ".mysql_prefix."email_ads where hrlock>0");
    while($row=@mysql_fetch_array($getrow))
    {
        $deltime=$row['hrlock']*60*60;
        mysql_query("DELETE FROM ".mysql_prefix."paid_clicks_$row[emailid] WHERE time<=DATE_SUB(NOW(), INTERVAL $deltime SECOND)");
    }

}
}

return;
?>