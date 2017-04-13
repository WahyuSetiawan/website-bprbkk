<?php
echo "
<form method=POST action='modul/function/penentuKeputusan.php' align='center'>
	<table>
        <tr>
        <td colspan=2><b><center>Proses Prediksi</center></b></td>
        </tr>";

        include "form_anggota.html";

        echo "<tr>
        <td colspan=2>
		<input type=submit value=Prediksi>
		</td>
        </tr>
    </table>
</form>";
?>