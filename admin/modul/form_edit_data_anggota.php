<?php
$query = mysql_query("SELECT * FROM data_nasabah WHERE id='$_GET[id]'");
$data = mysql_fetch_array($query);

echo "<form method=POST action=./aksi.php?module=data_anggota&act=update_data_anggota>
      <input type=hidden name=id value=$data[id]>
      <table>
        <tr>
        
        </tr>

        <tr>
        <td>No KTP</td>        
        <td>: 
            <input name='noktp' value='$data[noktp]' type='text'>
        </td>
        </tr>

        <tr>
        <td>Nama</td>        
        <td>: 
             <input name='nama' value='$data[nama]' type='text'>
        </td>
        </tr>

        <tr>
		<td>Pinjaman</td>        
		<td>: 
		<select name='pinjaman' type='text'>
		<option value='$data[pinjaman]' selected='selected'>$data[pinjaman]</option>
		<option value='Low'>Low</option>
		<option value='Upper'>Upper</option>
		<option value='High'>High</option>
        </select>
		</td>
		</tr>

        <tr>
		<td>Penghasilan</td>        
		<td>: 
		<select name='penghasilan' type='text'>
		<option value='$data[penghasilan]' selected='selected'>$data[penghasilan]</option>
		<option value='Low'>Low</option>
		<option value='Upper'>Upper</option>
		<option value='High'>High</option>
		</select>
		</td>
		</tr>

	<tr>
	<td>Angunan</td>        
	<td>: 
    <select name='angunan' type='text'>
	<option value='$data[angunan]' selected='selected'>$data[angunan]</option>
    <option value='Low'>Low</option>
    <option value='Upper'>Upper</option>
    <option value='High'>High</option>
        </select>
	</td>
	</tr>

    <tr>
	<td>BLBI</td>        
	<td>: 
	<select name='blbi' type='text'>
	<option value='$data[blbi]' selected='selected'>$data[blbi]</option>
    <option value='Termasuk'>Termasuk</option>
    <option value='TidakTermasuk'>Tidak Termasuk</option>
    </select>
	</td>
	</tr>


	<tr>
	<td>Syarat</td>        
	<td>: 
	<select name='syarat' type='text'>
    <option value='$data[syarat]' selected='selected'>$data[syarat]</option>
	<option value='Lengkap'>Lengkap</option>
    <option value='Kurang'>Kurang</option>
	</select>
	</td>
	</tr>
        
        <tr>
        <td><b>Kelayakan<b></td>        
        <td>: 
            <select name='kelayakan' type='text'>
            <option value='$data[kelayakan]' selected='selected'>$data[kelayakan]</option>
            <option value='Ya'>Ya</option>
            <option value='Tidak'>Tidak</option>
            </select>
        </td>
        </tr>

        <tr>
        <td colspan=2>
        <input type=submit value=Simpan><input type=button value=Batal onclick=self.history.back()>
        </td>
        </tr>
  </table>
  </form>";
?>