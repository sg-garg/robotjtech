<?php
include("functions.inc.php");
if (md5(domain.'89yoliogp9suiogbnkhjo94u984yu7895t4yi5goi34h5908y34958yihkjfhskdfh98y495uhkjehrkh943y85ihuktrnkwjhfo84987598347508hf,jshdkfhkjh4938y598345hkjhdkfjh983459yijhkf89649786593745jkhkds9f878979345').md5(domain.'8y4ukhih8gfkjnkjnbi88yrti98hykjbdk09ulfj;okdpf0ua098dyfkabkdfnkihioy80489yhiyugu6t8T8yt*O&T&*^TGKJUTO*&RT&UFUKYTG*(YU{))I("POJKJHJ<HGUK&T*I&TTURDFJHo;iy98676467587yluhjkugy8it8igjklgil8y9oli')!=serialnumber)
exit('Invalid Serial Number');

$title='eMail Blocker';
admin_login();

//-----------------------------------
// New block added
//-----------------------------------
if ($_POST['save'] == 'Add Block' AND !empty($_POST['blockaddress']) AND $_POST['new'] == 'true')
{
    mysql_query("insert into ".mysql_prefix."emails set address='".mysql_real_escape_string($_POST['blockaddress'])."', comment='".mysql_real_escape_string($_POST['note'])."'");
}
//-----------------------------------
// Block Edited
//-----------------------------------
if ($_POST['save'] == 'Add Block' AND !empty($_POST['blockaddress']) AND $_POST['new'] == 'false')
{
    $query = "UPDATE ".mysql_prefix."emails SET address='".mysql_real_escape_string($_POST['blockaddress'])."', comment='".mysql_real_escape_string($_POST['note'])."' WHERE address ='".mysql_real_escape_string($_POST['oldaddress'])."'";
    mysql_query($query);
}
//-----------------------------------
// Block Removed
//-----------------------------------
if ($_POST['save'] == 'Unblock')
{
    mysql_query("delete from ".mysql_prefix."emails where address='".mysql_real_escape_string($_POST['address'])."'");
}

//-----------------------------------
// Fetch current blocks
//-----------------------------------
$report = mysql_query("select * from ".mysql_prefix."emails order by address");

//-----------------------------------
// Are we editing a block?
//-----------------------------------
if ($_POST['save'] == 'Edit')
{
    $new = 'false';
    list($address, $note) = mysql_fetch_array(mysql_query("select address,comment from ".mysql_prefix."emails WHERE address = '".mysql_real_escape_string($_POST['address'])."'"));
}else{
    $new = 'true';
    $address = '';
    $note = 'Blocked on '.gmdate(timeformat,unixtime);
}
?>

<form method='POST' action='<?php echo $_SERVER['PHP_SELF'] ?>'>
<input type='hidden' name='new' value='<?php echo $new;?>'>
<input type='hidden' name='oldaddress' value='<?php echo $address;?>'>
Use <b>%</b> as a wild card. ex: If you wish to block all emails in a domain ending with hotmail.com then put <b>%@hotmail.com</b> in the field below
If you want to block just an email address like bob@hotmail.com just enter <b>bob@hotmail.com</b> in the field below

<br>
<table border='0'>
<tr>
    <td>Address:</td>
    <td><input style='width: 300px' type='text' size='30' maxlength='64' name='blockaddress' value='<?php echo $address;?>'></td>
</tr>
<tr>
    <td align='right'>Note:</td>
    <td><input style='width: 300px' type='text' size='30' maxlength='255' name='note'  value='<?php echo $note;?>'></td>
</tr>
<tr>
    <td colspan='2' align='center'><input type='submit' name='save' value='Add Block'></td>
</tr>
</table>
</form>
<br>

<table class='centered' cellpadding='2' cellspacing='0' border='1'>
<tr>
    <th>Address</th>
    <th>Note</th>
    <th>Action</th>
</tr>

<?php
while($row=mysql_fetch_array($report))
{
    if($bgcolor == ' class="row1"')
    {
        $bgcolor=' class="row2"';
    }else{
        $bgcolor=' class="row1"';
    }
    $counter++;
    $checked='';
    echo "\n<tr $bgcolor>
            <td>
                ".$row['address']."
            </td>
            <td>
                ".$row['comment']."
            </td>
            <td>
                <form method='POST' action='".$_SERVER['PHP_SELF']."'>
                <input type='hidden' name='address' value='".$row['address']."'>
                <input type='submit' name='save' value='Unblock'>
                <input type='submit' name='save' value='Edit'>
                </form>
            </td>
        </tr>";
}
?>

</table>
<br>

<?php footer(); ?>