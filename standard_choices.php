<?php 
	
	/*
	* Copyright 2015 Hamilton City School District	
	* 		
	* This program is free software: you can redistribute it and/or modify
    * it under the terms of the GNU General Public License as published by
    * the Free Software Foundation, either version 3 of the License, or
    * (at your option) any later version.
	* 
    * This program is distributed in the hope that it will be useful,
    * but WITHOUT ANY WARRANTY; without even the implied warranty of
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    * GNU General Public License for more details.
	* 
    * You should have received a copy of the GNU General Public License
    * along with this program.  If not, see <http://www.gnu.org/licenses/>.
    */
	
	//Required configuration files
	require_once(dirname(__FILE__) . '/../../core/abre_verification.php');
	require(dirname(__FILE__) . '/../../configuration.php');
	require_once('permissions.php');
	
	if($pagerestrictions=="")
	{
		if(isset($_GET["subject"])){ $subject=base64_decode(htmlspecialchars($_GET["subject"], ENT_QUOTES)); }else{ $subject=""; }
		
		echo "<select name='question_standard' id='question_standard' required>";
		echo "<option value='' selected='selected'></option>";
		
		if($subject=="Language Arts")
		{
			echo "<option value='CCSS.ELA-Literacy.L.4.5c'>CCSS.ELA-Literacy.L.4.5c</option>";
		}
		
		echo "</select><label>Standard</label>";
		
	}

?>

<script>
	
	//Material dropdown
	$('#question_standard').material_select();
		
</script>