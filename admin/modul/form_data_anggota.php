<?php
echo "
<form method=POST action='./aksi.php?module=data_anggota&act=input' align='center'>
	<table>";

        include "form_anggota.html";

        echo "<tr>
        <td>Kelayakan</td>        
        <td>: 
            <select name='kelayakan' type='text'>
			<option value='Ya'>Ya</option>
            <option value='Tidak'>Tidak</option>
            </select>
        </td>
        </tr>

		<tr>
        <td colspan=2>
		<input type=submit value=Input>
		</td>
        </tr>
    </table>
</form>";
?>