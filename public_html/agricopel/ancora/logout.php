<?
session_start();
session_destroy(); // Destrói a sessão limpando todos os valores salvos
?>

<script language = 'javascript'> 
alert('Você saiu do sistema LawSmart.')
location.href = '../login.php?';

</script>	

    <!--
         ?><script language = 'javascript'> location.href = 'login.php?msg=Voce saiu do sistema!'; </script>	 
    -->