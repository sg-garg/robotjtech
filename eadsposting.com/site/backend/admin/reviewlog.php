<?php
include("functions.inc.php");
if (md5(domain.'89yoliogp9suiogbnkhjo94u984yu7895t4yi5goi34h5908y34958yihkjfhskdfh98y495uhkjehrkh943y85ihuktrnkwjhfo84987598347508hf,jshdkfhkjh4938y598345hkjhdkfjh983459yijhkf89649786593745jkhkds9f878979345').md5(domain.'8y4ukhih8gfkjnkjnbi88yrti98hykjbdk09ulfj;okdpf0ua098dyfkabkdfnkihioy80489yhiyugu6t8T8yt*O&T&*^TGKJUTO*&RT&UFUKYTG*(YU{))I("POJKJHJ<HGUK&T*I&TTURDFJHo;iy98676467587yluhjkugy8it8igjklgil8y9oli')!=serialnumber)
exit('Invalid Serial Number');

$id = (int)$_POST['id'];

$title='Review Log For Paid Review Ad '.$id;
admin_login();

include("reviewadmgr_menu.php");

echo "<br>";

$report=@mysql_query("select * from ".mysql_prefix."paid_reviews_$id order by time desc");
echo "<table class='centered' border=1 cellpadding='2' cellspacing='0'><tr><td><b>Username</b></td><td><b>Date</b></td><td><b>Rating</b></td><td><b>Review</b>";
while($row=@mysql_fetch_array($report))
{
    if($bgcolor == ' class="row1" ')
    {
        $bgcolor=' class="row2" ';
    }
    else
    {
        $bgcolor=' class="row1" ';
    }
    $row['time']=mytimeread($row['time']);
    echo "</td></tr><tr $bgcolor><td><a href=viewuser.php?userid=$row[username] target=_viewuser>$row[username]</a></td><td>$row[time]</td><td>$row[rating]</td><td>$row[review]";

}
echo "</td></tr></table>";

footer();?>

