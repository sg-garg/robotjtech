<?
    include("functions.inc.php");

        if (md5(domain . '89yoliogp9suiogbnkhjo94u984yu7895t4yi5goi34h5908y34958yihkjfhskdfh98y495uhkjehrkh943y85ihuktrnkwjhfo84987598347508hf,jshdkfhkjh4938y598345hkjhdkfjh983459yijhkf89649786593745jkhkds9f878979345')
            . md5(
                  domain
                      . '8y4ukhih8gfkjnkjnbi88yrti98hykjbdk09ulfj;okdpf0ua098dyfkabkdfnkihioy80489yhiyugu6t8T8yt*O&T&*^TGKJUTO*&RT&UFUKYTG*(YU{))I("POJKJHJ<HGUK&T*I&TTURDFJHo;iy98676467587yluhjkugy8it8igjklgil8y9oli')
            != serialnumber)
            exit('Invalid Serial Number');

        if ($_POST[p_load])
            $ptarget=$_POST[p_load];

        if ($_GET[p_load])
            $ptarget=$_GET[p_load];

        $title='Plugin: ' . $ptarget;
        admin_login();
        $function='admin_page';

        if ($_GET['function'])
            $function=$_GET['function'];

        if ($_POST['function'])
            $function=$_POST['function'];

        plugin($ptarget, $function);
        footer();
?>