<?php
include("functions.inc.php");
if (md5(domain.'89yoliogp9suiogbnkhjo94u984yu7895t4yi5goi34h5908y34958yihkjfhskdfh98y495uhkjehrkh943y85ihuktrnkwjhfo84987598347508hf,jshdkfhkjh4938y598345hkjhdkfjh983459yijhkf89649786593745jkhkds9f878979345').md5(domain.'8y4ukhih8gfkjnkjnbi88yrti98hykjbdk09ulfj;okdpf0ua098dyfkabkdfnkihioy80489yhiyugu6t8T8yt*O&T&*^TGKJUTO*&RT&UFUKYTG*(YU{))I("POJKJHJ<HGUK&T*I&TTURDFJHo;iy98676467587yluhjkugy8it8igjklgil8y9oli')!=serialnumber)
    exit('Invalid Serial Number');

if(!empty($_POST)) extract($_POST,EXTR_SKIP);
if(!empty($_GET)) extract($_GET,EXTR_SKIP);
$title='User Profile & Stats';
admin_login();

//-----------------------------------------
// Update bad turing/cheat counters - CC220
//-----------------------------------------
if($_POST['updatecounters'] == 'yes')
{
    mysql_query('UPDATE `'.mysql_prefix.'last_login` SET bad_turing = '. (int)$_POST['badturing'] .', cheat_links = '. (int)$_POST['cheatclicks'] .' WHERE username = "'. $userid .'" LIMIT 1');
}

if ($_POST['add'])
{
    $userid=strtolower(substr(preg_replace('([^a-zA-Z0-9])', '', $_POST['add']), 0, 16));
    mysql_query('insert into '.mysql_prefix.'users set username="'.$userid.'",email="'.$userid.'@'.unixtime.'.com",password="'.unixtime.'", signup_date = NOW()');
}

if ($_POST['save_notes'])
{
    @mysql_query('replace into '.mysql_prefix.'notes set username="'.$_POST['userid'].'",notes="'.addslashes($_POST['notes']).'"');
}

if (!$userid)
{
    delete_user('','');
}

$_SESSION['username']=$userid;

$levelcache='';

if ($_POST['user_form']=='userinfo' and isset($_POST['userform']['disable_turing']))
{
    mysql_query('update '.mysql_prefix.'users set disable_turing="'.$_POST['userform']['disable_turing'].'" where username="'.$_SESSION['username'].'"');
}
if ($_POST['user_form']=='userinfo' and isset($account_type))
{
    mysql_query('update '.mysql_prefix.'users set account_type="'.addslashes($account_type).'" where username="'.$userid.'"');
    if ($account_type)
    {
        $commission_amount=0;
        $free_refs=0;
        list($commission_amount,$free_refs)=@mysql_fetch_row(@mysql_query('select commission_amount,free_refs from '.mysql_prefix.'member_types where description = "'.addslashes($account_type).'"'));
        $commission_amount=$commission_amount/100000;
    }
    mysql_query("update ".mysql_prefix."users set free_refs='$free_refs',commission_amount=".$commission_amount."*100000 where username='$username'");
    mysql_query("delete from ".mysql_prefix."free_refs");
}
if ($_POST['user_form']=='userinfo' and isset($upgrade_expires))
{
    mysql_query('update '.mysql_prefix.'users set upgrade_expires="'.addslashes($upgrade_expires).' 00:00:00" where username="'.$userid.'"');
}
if ($username and $upline)
{
    if (strtolower($upline)!="no referrer")
    {
        $upline=substr(preg_replace("([^a-zA-Z0-9])", "", $upline),0,16);
    }
    $newupline=$upline;
    while($upline)
    {
        list($upline,$chkusername)=@mysql_fetch_row(@mysql_query("select upline,username from ".mysql_prefix."users where username='$upline' limit 1"));
        if (!$uplinecheck[$upline])
        {
            $uplinecheck[$upline]=1;}
        else {
            @mysql_query("update ".mysql_prefix."users set upline='' where username='$upline'");
            break;
        }
        if ($chkusername){$uplineexists=1;}
        if ($chkusername==$username or $upline==$newupline){   
        $cantdo=1;
        break;
        }
    }
    if (strtolower($newupline)=="no referrer")
    {
        $newupline='';
        $cantdo=0;
        $uplineexists=1;
    }
    if (!$cantdo)
    {
        @mysql_query("update ".mysql_prefix."users set upline='$newupline' where username='$username' limit 1");
        @mysql_query("update ".mysql_prefix."users set rebuild_stats_cache=1 where username='$username' limit 1");
    } 
}
if ($username and $description)
{
    $newamount=$newamount*100000;
    $type='cash';
    if (preg_match("/POINT/",$description))
    {
        $type='points';
    }else 
    {
        $newamount=$newamount*admin_cash_factor;
    }
    @mysql_query('insert into '.mysql_prefix.'accounting set transid="'.maketransid($_SESSION['username']).'",unixtime=0,username="'.$_SESSION[username].'",description="'.$description.'",type="'.$type.'",amount="'.$newamount.'"');
    @mysql_query("update ".mysql_prefix."accounting set amount='$newamount' where username='$username' and description='$description'");
}
$userinfo=@mysql_fetch_array(@mysql_query("select * from ".mysql_prefix."users where username='$userid'"));
$username=$userinfo['username'];
$password=$userinfo['password'];
//echo "<pre>";
//print_r($_SESSION);
//print_r($_COOKIE);
//echo "</pre>";
?>
<style>
    .viewuserfield {
        width: 330px;
    }
</style>
<table width="100%" border='0'>
<tr>
<td valign=top>
    <center>

        <table border='0'>
        <tr>
        <td width="110">
            Username:
        </td>
        <td width="430"> 
            <form name="loginas" style="margin:0px; padding:0px;" method=post  action=<?php echo pages_url;?>enter.php target='_membersarea'>
            <input type=hidden value=2 name=admin_form>
            <input type=hidden name=username value="<?php user("username");?>">
            <input type=hidden name=password value="<?php echo $_SESSION['admin_password'];?>">
            <b><?php user("username");?></b> 
            <a href="#" onClick="javascript:document.forms['loginas'].submit();">(Login as <?php user("username");?>)</a>
            </form>
        </td>
        </tr>
        <tr>
        <td>
            Status:
        </td>
        <td> 
            <?php 
                //------------------------------------------
                // Calculate if user is active and get some data
                //------------------------------------------
                list($lastip,$lastdate,$lastbrowser,$badturing, $cheatclicks) = mysql_fetch_row(mysql_query("select ip_host,time,browser,bad_turing,cheat_links from ".mysql_prefix."last_login where username='$userid'"));
                if ($lastdate)
                {
                    $lastlogin = mytimeread(substr($lastdate,0,4).'-'.substr($lastdate,4,2).'-'.substr($lastdate,6,2).' '.substr($lastdate,8,2).':00:00');
                    $lastdate=substr($lastdate,0,4).'-'.substr($lastdate,4,2).'-'.substr($lastdate,6,2);
                    $active = strtotime($lastdate);
                    if((unixtime - $active) > (nocreditdays * 86400) AND nocreditdays != '')
                    {
                        $active = FALSE;
                    }
                    else
                    {
                        $active = TRUE;
                    }
                }
                else
                {
                    $active = FALSE;
                }

                //------------------------------------------
                // Default values when there is no last_login -- CC220
                //------------------------------------------
                if(empty($lastlogin)) {
                    $lastlogin = '[ never ]';
                }

                if(empty($lastip)) {
                    $badturing = 'N/A';
                    $cheatclicks = 'N/A';
                    $lastip = '[ no logins ]';
                }

                if(empty($lastbrowser)) {
                    $lastbrowser = '[ no logins ]';
                }


                if($userinfo['account_type'] == 'suspended')
                {
                    echo "<font color='orange'>suspended</font>, last login $lastlogin<br>";
                }
                else if($userinfo['account_type'] == 'canceled')
                {
                    echo "<font color='red'>canceled</font>, last login $lastlogin<br>";
                }
                else if($userinfo['account_type'] == 'advertiser')
                {
                    echo "advertiser<br>";
                }
                else
                {
                    if(empty($userinfo['account_type']))
                        $account = "free member";
                    else
                        $account = $userinfo['account_type'];

                    if($active)
                        $status = "<font color='green'>Active";
                    else
                        $status = "<font color='grey'>Inactive";

                    echo "<b>$status $account</font></b>, last login $lastlogin<br>";
                }
            ?>
        </td>
        </tr>
        <tr>
        <td>
            <a href=viewuser.php?userid=<?php user('referrer');?>>Referred By / <a href=viewuser.php?userid=<?php user('upline');?>>Upline</a></a>:
        </td>
        <td>
            <form  style="margin:0px; padding:0px;" method='POST'>

            <?php $referrer = user("referrer", 'return');
                if(empty($referrer))
                {
                    echo "[ not available ]";
                }
                else
                {
                    echo $referrer;
                }
            ?>


            &nbsp;/&nbsp;
            <input type=hidden name=userid value='<?php echo $userid;?>'>
            <?php if (!$uplineexists and $newupline){ echo "<table  width=100% bgcolor=red><tr><td><font color=ffffff><center>Error! Can not place under $newupline. Member does not exists</td></tr></table>";} elseif ($cantdo){ echo "<table  width=100% bgcolor=red><tr><td><font color=ffffff><center>Error! Can not place under $newupline. An endless loop would be created.</td></tr></table>";}?>
            <input type=hidden name=username value=<?php user("username");?>>
            <input style="width: 140px;" type=text name=upline value='<?php user("upline");?>'> 
            <input type=submit value='Move'>
            </form>
        </td>
        </tr>


        <tr>
        <td colspan='2' style='height: 20px;'>
            <hr>
        </td>
        </tr>


        <tr>
        <td>
            Signup Date:
        </td>
        <td>
            <?php 
            $signupdate = user("signup_date", 'return');
            if(substr($signupdate,0,10) == "0000-00-00")
            {
                echo "[ not available ]";
            }
            else
            {
                echo mytimeread($signupdate);
            }?>
        </td>
        </tr>
        <tr>
        <td>
            Signup IP/Host:
        </td>
        <td>
            <?php 
            $signuphost = user("signup_ip_host", 'return');
            if(empty($signuphost))
            {
                echo "[ not available ]";
            }
            else
            {
                echo safeentities($signuphost);
            }?>
        </td>
        </tr>

        <tr>
        <td>
            Joined from:
        </td>
        <td>
            <?php 
                if(empty($userinfo['http_referer']))
                {
                    echo "[ not available ]";
                }
                else
                {
                    echo "<div style='width: 100%; overflow:hidden;'><a href='".safeentities($userinfo['http_referer'])."' target='joinedfrom' title='".safeentities($userinfo['http_referer'])."'>".safeentities($userinfo['http_referer'])."</a></div>";
                }
            ?>
        </td>
        </tr>


        <tr>
        <td>
            Last IP/Host:
        </td>
        <td>
            <?php echo safeentities($lastip); ?>
        </td>
        </tr>

        <tr>
        <td>
            Last browser:
        </td>
        <td>
            <?php echo safeentities($lastbrowser); ?>
        </td>
        </tr>

        <tr>
        <td>
            Clicks data:
        </td>
        <td>
            <form style="margin:0px; padding:0px;"  method=post>
            <input type=hidden name=userid value='<?php echo $userid;?>'>
            <input type=hidden name=updatecounters value='yes'>
            bad turing clicks: <input style='width: 30px;' type=text name=badturing value='<?php echo $badturing;?>'>, 
            cheat clicks: <input style='width: 30px;'  type=text name=cheatclicks value='<?php echo $cheatclicks;?>'>
            &nbsp;&nbsp;<input type='submit' value='Update'>
            </form>
        </td>
        </tr>

        <tr>
        <td colspan='2' style='height: 20px;'>
            <hr>
        </td>
        </tr>



    <tr>
    <td>
    <form style="margin:0px; padding:0px;"  method=post>
    <input type=hidden name=userid value='<?php echo $userid;?>'>
    <input type=hidden name=user_form value=userinfo>
    <input type=hidden name=required_keywords value=1>
    <input type=hidden name=required value='email,first_name,last_name,address,city,state,zipcode,country'>
    <?php form_errors("email","You must place an email address in the email address field","The email address you select is already in use please try another","<tr><td colspan=2 bgcolor=red align=center>","</td></tr>");?> 

        <a href=mailto:<?php user("email");?>>E-Mail:</a>
    </td>
    <td> 
        <input class="viewuserfield" type="text" name="userform[email]" value="<?php user("email");?>">
    </td>
    </tr>

    <tr>
    <td>
        Send emails to:
    </td>
    <td> 
        <select name='userform[email_setting]'>
        <?php existing_member_email_setting();?>
        </select>
    </td>
    </tr>
    <?php form_errors("first_name","You must place your first name in the first name field","N/A","<tr><td colspan=2 bgcolor=red align=center>","</td></tr>");?>
    <?php form_errors("last_name","You must place your last name in the last name field","N/A","<tr><td colspan=2 bgcolor=red align=center>","</td></tr>");?> 
    <tr>
    <td>First, Last Name:</td>
    <td> 
        <input style="width:163px;" type="text" name="userform[first_name]" value="<?php user("first_name");?>"> 
        <input style="width:163px;" type="text" name="userform[last_name]" value="<?php user("last_name");?>">
    </td>
    </tr>
    <?php form_errors("address","You must place your street address in the address field","N/A","<tr><td colspan=2 bgcolor=red align=center>","</td></tr>");?>
    <tr>
    <td>Address:</td>
    <td> 
        <input class="viewuserfield" type="text" name="userform[address]" value="<?php user("address");?>">
    </td>
    </tr>
    <?php form_errors("city","You must place your city in the city field","N/A","<tr><td colspan=2 bgcolor=red align=center>","</td></tr>");?>   
    <?php form_errors("zipcode","You must place your zip or postal code in the zip code field","N/A","<tr><td colspan=2 bgcolor=red align=center>","</td></tr>");?>
    <tr>
        <td>
            City, zipcode:
        </td>
        <td> 
            <input style="width:163px;" type="text" name="userform[city]" value="<?php user("city");?>">
            <input style="width:163px;" type="text" name="userform[zipcode]" value="<?php user("zipcode");?>">
        </td>
    </tr>
    <?php form_errors("state","You must place your state in the state field or type N/A if you do not have a state","N/A","<tr><td colspan=2 bgcolor=red align=center>","</td></tr>");?>
    <tr>
        <td>
            State:
        </td>
        <td> 
            <select class="viewuserfield" name="userform[state]">
            <option value=''>Please select your state
            <?php existing_member_states();?>
            </select>
            <br>
            Other: <input width="250" type="text" name="userform[state_other]" value="<?php user("state_other");?>">
        </td>
    </tr>            
    <?php form_errors("country","Please select your country","N/A","<tr><td colspan=2 bgcolor=red align=center>","</td></tr>");?>
    <tr>
        <td>
            Country:
        </td>
        <td> 
            <select name="userform[country]">
            <option value=''>Please select your country
            <?php existing_member_countries();?>
        </select>
        </td>
    </tr>
    <?php form_errors("vacation","You have entered an invalid date. Please use the format MM/DD/YYYY","N/A","<tr><td colspan=2 bgcolor=red align=center>","</td></tr>");?>
    <tr>
        <td>
            Vacation:
        </td>
        <td>
            <input type='date' name='userform[vacation]'
            value='<?php 
                list($vacation_date) = @mysql_fetch_array(@mysql_query('select vacation from ' . mysql_prefix . 'users where username="' . $_SESSION['username'] . '" limit 1')); 
                if($vacation_date == '0000-00-00')
                    echo '';
                else
                    echo $vacation_date;?>'>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="Save Changes"><br>(input format yyyy-mm-dd, blank to disable)
        </td>
    </tr>
    </table>

    </td>
    <td rowspan='2' width="170" valign='top'>
        Select categories of interests:<br>
    <?php 
        $getkeys=@mysql_query("select keyword from ".mysql_prefix."keywords");
        $idx=0;
        while($row=@mysql_fetch_row($getkeys))
        {
            echo "<input type='checkbox' class='checkbox' name='keyword[$idx]' value='".safeentities($row[0])."' ";
            interests($row[0],"checked");
            echo ">$row[0]<br>";
            $idx++;
        }
    ?>



</td>
</tr>

<tr>
<td>

    <hr>

    <center>
    <table>
    <tr>
    <td colspan=2 align=center>
        <b>Membership Type</b>

    </td>
    </tr>

            <tr>
            <td>
                Membership type:
            </td>
            <td>
        
            <?php $result=@mysql_query('select description from '.mysql_prefix.'member_types order by description');
            $selected='';
            if ($userinfo['account_type']=='advertiser')
            $selected='selected';
            echo '<select name=account_type><option value="">Use custom settings below<option value="advertiser" '.$selected.'>Advertiser';
            $selected='';
            if ($userinfo['account_type']=='suspended')
            $selected='selected';
            echo '<option value="suspended" '.$selected.'>Suspended';
            $selected='';
            if ($userinfo['account_type']=='canceled')
            $selected='selected';
            
            echo '<option value="canceled" '.$selected.'>Canceled';
            
            while ($row=mysql_fetch_row($result)){
                $selected='';
                if ($userinfo['account_type']==$row[0])
                $selected='selected';
                echo '<option '.$selected.' value="'.safeentities($row[0]).'">'.safeentities($row[0]);
            }
            ?>
        
            </select>
            </td>
            </tr>
            <tr>
            <td>
                Auto-expires:
            </td>
            <td>
                <input type='text' name='upgrade_expires' 
                value='<?php 
                list($upgrade_expires) = @mysql_fetch_array(@mysql_query('select upgrade_expires from ' . mysql_prefix . 'users where username="' . $_SESSION['username'] . '" limit 1')); 
                if(substr($upgrade_expires, 0, 4) == '0000')
                    echo '';
                else
                    echo substr($upgrade_expires, 0, 10);
                ?>'><br>
                (input format yyyy-mm-dd, blank to disable)
            </td>
            </tr>

        <tr>
        <td colspan='2' align='center'>
        <br>
        <b>Custom Settings</b>
        </td>
        </tr> 
        <tr>
            <td>
                Extra Commission (%):
            </td>
        <td>
        <input type=text name=commission_amount value=<?php echo number_format($userinfo['commission_amount']/100000,5);?>></td></tr>
        <tr>
            <td>
            Free Referrals?
            </td>
        <td>
            <input type=text name=free_refs value=<?php echo $userinfo[free_refs];?>>
        </td>
        </tr>
        <tr>
        <td colspan='2' align='center'>
            <font size=-2>Enter 0 to disable. Enter 1 or higher to enable. Accounts with higher numbers<br>will receive free referrals more often then accounts with lower numbers<br>
            <br>
        </td>
        </tr>
        <tr>
        <td>
            Turing Numbers:
        </td>
        <td>
            <select name=userform[disable_turing]>
            <option value='0' <?php if ($userinfo[disable_turing]==0){ echo 'selected';}?>>Enabled<option value='1' <?php if ($userinfo[disable_turing]==1){ echo
            'selected';}?>>Disabled
                </select>
        </td>
        </tr>
        </td>
        </tr>

        <tr><td colspan='2' style='height:8px'></td></tr>  

        <tr>
        <td>
            Payment method, account:
        </td>
        <td>
        <select style="width:95px;" name=userform[pay_type]>
        <option value=''>Please select
        <?php existing_member_paytypes();
        echo "</select>";?>
            <input style="width:200px;" type=text value="<?php user("pay_account");?>" name=userform[pay_account]>
        </td>
        </tr>

        <tr><td colspan='2' style='height:8px'></td></tr>  

        <?php form_errors("password","The password you entered did not match what you put in the confirmation field","N/A","<tr><td colspan=2 bgcolor=red align=center>","</td></tr>");?>
        <tr>
            <td>
                New Password:
            </td>
            <td>
                <input class="viewuserfield" type=password name=userform[password] value="">
            </td>
        </tr>
        <tr>
            <td>
                Confirm New Password:
            </td>
            <td>
                <input class="viewuserfield" type=password name=userform[confirm_password] value="">
            </td>
        </tr>
        <tr>
            <td colspan=2 align=center>
                <input type="submit" value="Save Changes">
            </td>
        </tr>         
        <input type=hidden value=1 name=admin_form>
        <input type=hidden value="<?php user("username");?>" name=username>
        <input type=hidden value="<?php user("password");?>" name=userform[alterinfo_password]>
        <input type=hidden value="<?php echo $_SESSION[admin_password];?>" name=password>
        </form>
    </table>
</td>
</tr>
</table>
</center>


<table style="width: 100%">
    <tr>
    <form method=post>
    <td align=center>
        <hr>
        <br>
        <b>Notes:</b><br>
        <textarea name=notes rows=10 style="width: 97%"><?php list($notes)=@mysql_fetch_row(@mysql_query('select notes from '.mysql_prefix.'notes where username="'.$_SESSION[username].'"'));echo safeentities($notes);?></textarea><br>
        <center>
        <input type=submit name=save_notes value='Save Notes'>
        <input type=hidden name=userid value=<?php echo $userid;?>>
        </center>
    </td>
    </tr>
    </form>
</table>


<hr>

<table style="width: 100%" border='0'>
<td align='center'>

    <table border='1'>
        <tr>
        <td> <center>Downline Count: <?php level_total('all','nocache');?></center>
            <?php show_levels();?>
            </form>
        </td>
        </tr>
    </table>

</td>
<td align='center'>

    <table border='0' cellpadding='2' cellspacing='0'>
        <tr>
            <td style='text-align: right ! important'><b>Direct cash earnings:</b></td>
            <td align=right><?php cash_earnings();?> </td>
        </tr>
        <tr>
            <td style='text-align: right ! important'><b>Downline cash earnings:</b></td>
            <td align=right><?php dlcash_earnings();?></td>
        </tr>
        <tr>
            <td style='text-align: right ! important'><b>Cash balance after all transactions:</b></td>
            <td align=right><?php cash_totals();?></td>
        </tr>
    </table>

</td>
<td align='left'>

    <table border='0' cellpadding='2' cellspacing='0'>
        <tr>
            <td style='text-align: right ! important'><b>Direct point earnings:</b></td>
            <td align=right><?php points_earnings();?></td>
        </tr>
        <tr>
            <td style='text-align: right ! important'><b>Downline point earnings:</b></td>
            <td align=right><?php dlpoints_earnings();?></td>
        </tr>
        <tr>
            <td style='text-align: right ! important'><b>Point balance after all transactions:</b></td>
            <td align=right><?php points_totals();?></td>
        </tr>
    </table>

</td>
</tr>
</table>

<br>
<hr>
<br>


<table border='0' width="100%">
    <tr>
    <td valign='top' align='right'>
        
        <table width="100%" border='1' cellpadding='2' cellspacing='0'>
            <tr><th colspan=3 align=center>Cash - <a href=transactions.php?usersearch=<?php user("username");?>&transtype=cash>(edit cash transactions)</a></td></tr>
            <?php cash_transactions("all","<tr><td align=right>","</td><td align=right>","</td></tr>","desc","yes","</td><td>",5,admin_cash_factor,10);?>
            <tr><td align=right><b>Total Cash:</b></td><td colspan=2 align=right><?php cash_totals("all",5,admin_cash_factor);?></td></tr>
        </table>

    </td>
    <td valign='top' align='left'>

        <table width="100%" border='1' cellpadding='2' cellspacing='0'>
            <th colspan=3 align=center>Points - <a href=transactions.php?usersearch=<?php user("username");?>&transtype=points>(edit point transactions)</a></td></tr>
            <?php point_transactions("all","<tr><td align=right>","</td><td align=right>","</td></tr>","desc","yes","</td><td>",5,1,10);?>          
            <tr><td align=right><b>Total Points:</b></td><td align=right colspan=2><?php points_totals('all',5);?></td></tr>             
        </table>

    </td>
    </tr>
</table>

<br>
<br>

<table border='1' cellpadding='2' cellspacing='0' width='100%'>
    <tr>
        <th colspan='2' align='center'>Referrals came from the following places</th>
    </tr>

    <tr>
        <th width=30>Total</th>
        <th>URL</th>
    </tr>
    <?php show_http_referer();?>
</table>

<br>


<center>Recent advertiser that this member has received credit for visiting</center><hr>
<?php latest_visits();?>
<?php 
$_SESSION[username]='';
footer();

?>
