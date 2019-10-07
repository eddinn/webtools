<html> 
<head>
<title>Vefuppsetning</title>
<head> 
<body>

<img SRC="../logo.gif" ALT="logo" height=95 width=148>
<h2>Uppsetning á Linux vef</h2>
<h4>Vefsvæði:  PHP/Perl/MySQL</h4> 

<P> <HR WIDTH=100%></P>

<form action="index.php" method="post"> 

URL fyrir vefinn:<br>
www.<input type=text name=web_url size=50> <p>

Notandanafn (ekki meira en 8 bókstafir) notað fyrir FTP og MySQL aðgang:<br>
<input type=text name=user_name size=8 maxlength=8> <p>

Tengiliður vefsins (ein lína: nafn, netfang, sími):<br>
<input type=text name=contact_name size=50> <p>
<P> <HR WIDTH=100%></P>

Senda upplýsingar um stofnun vefsins í tölvupósti til:
<br>hostmaster@localhost<br>
<br>
Ef þú vilt senda viðbótarupplýsingar í tölvupósti, skrifaðu þær þá hér:<br>
<textarea NAME="mailBody" WRAP=SOFT COLS=75 ROWS=9 TABINDEX=5></textarea><br>

<input type=Submit value="Stofna Vef" name=my_selection>

<P> <HR WIDTH=100%></P>

<input type=Submit value="Lista MySql Grunna" name=my_selection>
<!-- <input type=Submit value="Lista Diskanotkun" name=my_selection> -->
<input type=Submit value="Notendur og passwd" name=my_selection>
</form>

<?php
//phpinfo();
//************************************************************
//$syscmdfile="/etc/httpd/conf/web_nyttdomain.sh";
$syscmdfile="/etc/httpd/conf/web_create.sh";
$tmp_file="/etc/httpd/conf/web_create/web_create.tmp";
//----------------------------------------------------------
if ($_REQUEST["my_selection"] == "Lista MySql Grunna") {
    $grunnar = shell_exec( '/usr/bin/sudo /usr/bin/mysqlshow -uroot -p' );
  printf("<pre>%s</pre>", $grunnar);
}
//----------------------------------------------------------
//if ($_REQUEST["my_selection"] == "Lista Diskanotkun") {
//  $diskur = shell_exec( "cat /var/www/virtual/Notkun" );
//  printf("<pre>%s</pre>", $diskur);
//}
//----------------------------------------------------------
if ($_REQUEST["my_selection"] == "Notendur og passwd") {
  #$listi = shell_exec( "cat /etc/httpd/conf/listi-vefja.log | grep 'VEFUR:'" );
  # Breyti < og > í &lt; og &gt;
  $listi = shell_exec( "cat /etc/httpd/conf/listi-vefja.log | grep 'VEFUR:' | sed 's/</\&lt;/g' | sed 's/>/\&gt;/g' " );
  printf("<pre>%s</pre>", $listi);
}
//----------------------------------------------------------
if ($_REQUEST["my_selection"] == "Stofna Vef") {
    $web_url = $_REQUEST["web_url"];
    $user_name = $_REQUEST["user_name"];
    $contact_name = $_REQUEST["contact_name"];
    $mailBody = $_REQUEST["mailBody"];
    if ( $web_url != "" && $user_name !=""){
        if (check_user_exists($user_name)){
            exit("<br> Notandi  $user_name er til i kerfinu !!</br><br>Veldu annad notendanafn </br>");
        }
//	printf("<P><B>Vefur verður stofnaður:</B></P>\n");
 //       printf("Vefur: www.%s<br>\n", $web_url);
  //      printf("Notandi: %s<br>\n", $user_name);
   //     printf("Lykilorð: %s<br>\n", $user_passwd);
    //    printf("Tengiliður: %s<br>\n", $contact_name);
        $user_passwd=generate_password(7,true,null);
        $text_to_file  = "NEW_DOMAIN=$web_url\n";
        $text_to_file .= "NEW_HOST=www\n";
        $text_to_file .= "NEW_USER=$user_name\n";
        $text_to_file .= "NEW_PASS=$user_passwd\n";
        $text_to_file .= "NEW_CONTACT='$contact_name'\n";
        write_to_file($tmp_file,$text_to_file);
        sleep (1);
        documentation($web_url,$user_name,$user_passwd);
        system("sudo $syscmdfile >> /etc/httpd/conf/web_create/loggur.txt 2>&1 ");
    } else {
        if ( $web_url == "") {
            print "<br> Fylltu út í reitinn: URL fyrir vefinn !!</br>";
        }
        if ( $user_name =="") {
            print "<br> Fylltu út í reitinn: Notendanafn !!</br>";
        }
    }
}
//------------------------------------------
//if ($my_selection == "Tester") {
//   $is_user=check_user_exists($user_name);
//    if ($is_user) print "<br> user = $is_user </br>";
//}
//------------------------------------------
//************************************************************
function check_user_exists($username){
    //athuga hvort user er til i /etc/passwd
    $file   = "/etc/passwd";
    $rights = "r";
    $return_value=1;
    $passwd_file="";
    $fp = fopen($file, $rights);
    while (!feof($fp)){  $passwd_file .= fgets($fp,2);  }
   
    if (!($temp = stristr($passwd_file, "$username:")) == "") {
        //echo "User Exists.\n";
        $return_value=1;
    } else {
        //echo "False! User not Found!!\n";
        $return_value= 0;
    }

    $temp = "";
    fclose($fp);
    return $return_value;

}
//************************************************************
function check_dns($web_url){
    if (checkdnsrr($web_url,"ANY")) {
        print("\n\ncheckndsrr: TRUE ");
    } else {
        print("\n\ncheckndsrr: FALSE ");
    }
}// end check_dns
//************************************************************
function write_to_file($to_file,$text){
    $file = fopen($to_file, 'w');
    fwrite($file, $text);
    fclose($file);
}//end write_to_file
//*******************************************************
//************************************************************
function documentation( $web_url, $user_name, $PASSWORD) {
    global $mailBody;
    $my_hostname=$_SERVER['SERVER_NAME'];
    $to="hostmaster@localhost";
    $headers  = "MIME-Version: 1.0\r \n";
    $headers .= "Content-type: text/plain; charset=utf-8\r \n";
    $headers .= "From: Vefumsjón <webmaster@$my_hostname>\r\n";
    $headers .= "Reply-To: webmaster@localhost\r \n";
    $headers .= "X-Priority: 3\r \n";
    $headers .= "X-MSMail-Priority: High\r \n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    $Subject="Nýr Linux vefur stofnadur: www.$web_url";
    $Msg = " \n
Nýr vefur www.$web_url hefur verið stofnaður á $my_hostname,
Hann er í flokki php/perl/mysql.
Lykilorð notandans \"$user_name \" er \"$PASSWORD\"
        
        \n
FTP Aðgangsupplýsingar:
host: $my_hostname 
user: $user_name
password: $PASSWORD
MySQL gagngrunnur \"$user_name\" hefur verið uppsettur.
PHPMyadmin er uppsett:  http://
        
        \n";
    $Msg .= $_REQUEST["mailBody"];
    $Msg .= "\n\n";
    $Msg .="Webmaster.\n ";
 
    mail($to,$Subject,$Msg,$headers);
  
}//end documentation
//*******************************************************
function generate_password($digits,$c,$st)
{
   if(!ereg("^([4-9]|((1|2){1}[0-9]{1}))$",$digits)) // 4-29 chars allowed
      $digits=4;
   for(;;)
   {
      $pwd=null; $o=null;
      // Generates the password ....
      for ($x=0;$x<$digits;)
      {
         $y = rand(1,1000);
         if($y>350 && $y<601) $d=chr(rand(48,57));
         if($y<351) $d=chr(rand(65,90));
         if($y>600) $d=chr(rand(97,122));
         if($d!=$o)
         {
            $o=$d; $pwd.=$d; $x++;
         }
      }
      // if you want that the user will not be confused by O or 0 ("Oh" or "Null")
      // or 1 or l ("One" or "L"), set $c=true;
      if($c)
      {
         $pwd=eregi_replace("(l|i)","1",$pwd);
         $pwd=eregi_replace("(o)","0",$pwd);
      }
      // If the PW fits your purpose (e.g. this regexpression) return it, else make a new one
      // (You can change this regular-expression how you want ....)
      if(ereg("^[a-zA-Z]{1}([a-zA-Z]+[0-9][a-zA-Z]+)+",$pwd))
         break;
   }
   if($st=="L") $pwd=strtolower($pwd);
   if($st=="U") $pwd=strtoupper($pwd);
   return $pwd;
}//end generate_password
//*******************************************************

//************************************************************
//print "<br> my_selection = $my_selection <br>";
?>

</body>
</html>

