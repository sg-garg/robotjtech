<?php
include("functions.inc.php");
if (md5(domain.'89yoliogp9suiogbnkhjo94u984yu7895t4yi5goi34h5908y34958yihkjfhskdfh98y495uhkjehrkh943y85ihuktrnkwjhfo84987598347508hf,jshdkfhkjh4938y598345hkjhdkfjh983459yijhkf89649786593745jkhkds9f878979345').md5(domain.'8y4ukhih8gfkjnkjnbi88yrti98hykjbdk09ulfj;okdpf0ua098dyfkabkdfnkihioy80489yhiyugu6t8T8yt*O&T&*^TGKJUTO*&RT&UFUKYTG*(YU{))I("POJKJHJ<HGUK&T*I&TTURDFJHo;iy98676467587yluhjkugy8it8igjklgil8y9oli')!=serialnumber)
exit('Invalid Serial Number');

$title='State Selections';
admin_login();

if ($_POST['delete'])
{
    @mysql_query("delete from ".mysql_prefix."states where state='". mysql_real_escape_string($_POST['delete'])."' LIMIT 1");
}
if ($_POST['add'])
{
    @mysql_query("insert into ".mysql_prefix."states set state='". mysql_real_escape_string($_POST['add'])."'");
} 
$results=@mysql_query("select * from ".mysql_prefix."states order by state");
?>

<form method=post>
Add state: <input type=text name=add><input type=submit value=Add></form><br><br>

<table border='1' cellpadding='2' cellspacing='0'>

<?php
while ($row = mysql_fetch_array($results))
{
    if($bgcolor == 'class="row1"')
    {$bgcolor = 'class="row2"';}
    else
    {$bgcolor = 'class="row1"';}

    echo "<tr $bgcolor><td>$row[state]</td>
            <form method=post><td>
            <input type=hidden name=delete value='". safeentities($row['state']) ."'>
            <input type=submit value='Delete'>
        </td></tr>
         </form>\n\n";
}
echo "</table>";
footer(); 
