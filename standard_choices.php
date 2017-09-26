<?php 
	
	/*
	* Copyright (C) 2016-2017 Abre.io LLC
	*
	* This program is free software: you can redistribute it and/or modify
    * it under the terms of the Affero General Public License version 3
    * as published by the Free Software Foundation.
	*
    * This program is distributed in the hope that it will be useful,
    * but WITHOUT ANY WARRANTY; without even the implied warranty of
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    * GNU Affero General Public License for more details.
	*
    * You should have received a copy of the Affero General Public License
    * version 3 along with this program.  If not, see https://www.gnu.org/licenses/agpl-3.0.en.html.
    */
	
	//Required configuration files
	require_once(dirname(__FILE__) . '/../../core/abre_verification.php');
	require_once(dirname(__FILE__) . '/../../core/abre_functions.php');
	require(dirname(__FILE__) . '/../../configuration.php');
	require_once('permissions.php');
	
	if($pagerestrictions=="")
	{
		if(isset($_GET["subject"])){ $subject=base64_decode(htmlspecialchars($_GET["subject"], ENT_QUOTES)); }else{ $subject=""; }
		
		echo "<select name='question_standard' id='question_standard' required>";
		echo "<option value='' selected='selected'></option>";
		
			$query = "SELECT * FROM assessments_standards where Subject='$subject' order by Standard";
			$dbreturn = databasequery($query);
			foreach ($dbreturn as $value)
			{
				$Standard=$value["Standard"];
				$Standard_Text = str_replace("CCSS.Math.Content.","",$Standard);
				$Standard_Text = str_replace("CCSS.ELA-Literacy.","",$Standard_Text);
				echo "<option value='$Standard'>$Standard_Text</option>";
			}
		
		echo "</select><label>Standard</label>";
		
	}

?>

<script>
	
	//Material dropdown
	$('#question_standard').material_select();
		
</script>