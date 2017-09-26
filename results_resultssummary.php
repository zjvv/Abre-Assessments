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
	require(dirname(__FILE__) . '/../../configuration.php'); 
	require_once(dirname(__FILE__) . '/../../core/abre_verification.php'); 
	require(dirname(__FILE__) . '/../../core/abre_dbconnect.php'); 
	require_once('../../core/abre_functions.php');
	require_once('permissions.php');
	
	if($pagerestrictions=="")
	{
		
		$Assessment_ID=htmlspecialchars($_GET["assessmentid"], ENT_QUOTES);
		$sql = "SELECT * FROM assessments_status where Assessment_ID='$Assessment_ID'";
		$result = $db->query($sql);
		$rowcount=mysqli_num_rows($result);
		if($rowcount!=0)
		{
			?>
			<div class='page_container mdl-shadow--4dp'>
			<div class='page'>
				<div id='searchresults'>	
					<div class='row'><div class='col s12'>
						<table id='myTable' class='tablesorter'>
							<thead>
								<tr class='pointer'>
									<th>User</th>
									<th>Start Time</th>
									<th>End Time</th>
								</tr>
							</thead>
							<tbody>
								
								<?php
								$sql = "SELECT * FROM assessments_status where Assessment_ID='$Assessment_ID' order by User";
								$result = $db->query($sql);
								while($row = $result->fetch_assoc())
								{
									$User=htmlspecialchars($row["User"], ENT_QUOTES);
									$Start_Time=htmlspecialchars($row["Start_Time"], ENT_QUOTES);
									$Start_Time=date("F j, Y, g:i A", strtotime($Start_Time));
									$End_Time=htmlspecialchars($row["End_Time"], ENT_QUOTES);
									if($End_Time=="0000-00-00 00:00:00"){ $End_Time="In Progress"; }else{ $End_Time=date("F j, Y, g:i A", strtotime($End_Time)); }
								
									echo "<tr class='assessmentrow'>";
										echo "<td>$User</td>";
										echo "<td>$Start_Time</td>";
										echo "<td>$End_Time</td>";
									echo "</tr>";
								}
								echo "</tbody>";
							echo "</table>";
						echo "</div>";
						
					echo "</div>";
		
			echo "</div>";
			echo "</div>";
		}
		else
		{
			echo "<div class='row center-align'><div class='col s12'><h6>There are no results for this assessment</h6></div></div>";
		}
		
	}

?>
		
<script>
			
	//Process the profile form
	$(function()
	{
				
		$("#myTable").tablesorter();
					
	});
	
				
</script>