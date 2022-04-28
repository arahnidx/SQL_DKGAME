<?php 

					$busca_login = mysql_query($conn, "SELECT * FROM Player WHERE PlayerID='".$str_login."' and SSN='".$str_senha."'");
					$conta_login = mysql_num_rows($conn, $busca_login);
					$resgata_login = mysql_fetch_array($conn, $busca_login);

?>