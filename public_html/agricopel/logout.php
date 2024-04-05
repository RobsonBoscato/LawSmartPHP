<?
session_start();

session_destroy(); // Destrói a sessão limpando todos os valores salvos

?>

<script language = 'javascript'> 
alert('You have successfully logout from LawSmart')
location.href = 'login.php?';

</script>	


    <!--
         ?><script language = 'javascript'> location.href = 'login.php?msg=Voce saiu do sistema!'; </script>	 
    -->