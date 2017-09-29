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
	require_once(dirname(__FILE__) . '/../../core/abre_dbconnect.php');
	require_once(dirname(__FILE__) . '/../../core/abre_functions.php');
	require_once('logic.php');
	require_once('functions.php');
	require_once('permissions.php');
	
	if($pagerestrictions=="")
	{
		
		$sql = "SELECT * FROM assessments where Verified='1' order by Title";
		$result = $db->query($sql);
		$rowcount=mysqli_num_rows($result);
		if($rowcount!=0)
		{
			echo "<div class='mdl-shadow--2dp' style='background-color:#fff; padding:20px 40px 40px 40px'>";
			echo "<div class='row' style='padding:15px;'>";
		?>
			<table id='myTable' class='tablesorter bordered'>
				<thead>
					<tr class='pointer'>
						<th>Name</th>
						<th class='hide-on-med-and-down'>Level</th>
						<th style='width:100px'></th>
						<th style='width:100px'></th>
					</tr>
				</thead>
				<tbody>
								
				<?php
					$sql = "SELECT * FROM assessments where Verified='1' order by Title";
					$result = $db->query($sql);
					while($row = $result->fetch_assoc())
					{
						$Assessment_ID=htmlspecialchars($row["ID"], ENT_QUOTES);
						$Title=htmlspecialchars($row["Title"], ENT_QUOTES);
						$Description=htmlspecialchars($row["Description"], ENT_QUOTES);
						$Subject=htmlspecialchars($row["Subject"], ENT_QUOTES);
						$Level=htmlspecialchars($row["Level"], ENT_QUOTES);
						$Grade=htmlspecialchars($row["Grade"], ENT_QUOTES);
						$Locked=htmlspecialchars($row["Locked"], ENT_QUOTES);
						$Shared=htmlspecialchars($row["Shared"], ENT_QUOTES);
						$Verified=htmlspecialchars($row["Verified"], ENT_QUOTES);
						$Owner=htmlspecialchars($row["Owner"], ENT_QUOTES);
						$Editors=htmlspecialchars($row["Editors"], ENT_QUOTES);
						$Session_ID=htmlspecialchars($row["Session_ID"], ENT_QUOTES);
									
						if (strpos($Editors, $_SESSION['useremail']) !== false) { $SharedEditable=1; }else{ $SharedEditable=0; }
									
						$Student_Link="$portal_root/?url=assessments/session/$Assessment_ID/$Session_ID";
						
						//Check to see if Guided Learning Code Created
						$sql2 = "SELECT * FROM `guide_links` WHERE `Data` LIKE '%$Student_Link%'";
						$result2 = $db->query($sql2);
						$GLCode="Code not yet available";
						while($row2 = $result2->fetch_assoc())
						{
							$Board_ID=htmlspecialchars($row2["Board_ID"], ENT_QUOTES);
							
							//Get the Code
							$sql3 = "SELECT * FROM `guide_boards` where ID='$Board_ID'";
							$result3 = $db->query($sql3);
							while($row3 = $result3->fetch_assoc())
							{
								$GLCode=htmlspecialchars($row3["Code"], ENT_QUOTES);
							}
						}
								
						echo "<tr class='assessmentrow'>";
							echo "<td>$Title</td>";
							echo "<td class='hide-on-med-and-down'>$Level</td>";
							echo "<td width=100px>";		
								echo "<a class='waves-effect waves-light btn modal-giveassessment' href='#giveassessment' data-givetitle='Guided Learning Code' data-givelink='$GLCode' style='background-color:"; echo sitesettings("sitecolor"); echo "'>Give</a>";	
							echo "</td>";
							echo "<td width=100px>";		
								echo "<a class='waves-effect waves-light btn clicklink' href='#assessments/results/$Assessment_ID' style='background-color:"; echo sitesettings("sitecolor"); echo "'>Results</a>";	
							echo "</td>";
						echo "</tr>";
					}
				echo "</tbody>";
			echo "</table>";
			
			echo "</div>";
			echo "</div>";
		}
		else
		{
			echo "<div class='row center-align'><div class='col s12'><h6>District assessments</h6></div><div class='col s12'>Assessments that the district creates will appear here.</div></div>";
		}
		
	}
		
?>