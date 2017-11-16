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
		
		$sql = "SELECT * FROM assessments where Shared='1' order by Title";
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
									<th style='width:50px'></th>
									<th>Title</th>
									<th class='hide-on-med-and-down'>Subject</th>
									<th class='hide-on-med-and-down'>Grade Level</th>
									<th style='width:30px'></th>
								</tr>
							</thead>
							<tbody>
								
								<?php
								$sql = "SELECT * FROM assessments where Shared='1' order by Title";
								$result = $db->query($sql);
								while($row = $result->fetch_assoc())
								{
									$Assessment_ID=htmlspecialchars($row["ID"], ENT_QUOTES);
									$Title=htmlspecialchars($row["Title"], ENT_QUOTES);
									$Description=htmlspecialchars($row["Description"], ENT_QUOTES);
									$Subject=htmlspecialchars($row["Subject"], ENT_QUOTES);
									$Grade=htmlspecialchars($row["Grade"], ENT_QUOTES);
									$Locked=htmlspecialchars($row["Locked"], ENT_QUOTES);
									$Shared=htmlspecialchars($row["Shared"], ENT_QUOTES);
									$Verified=htmlspecialchars($row["Verified"], ENT_QUOTES);
									$Owner=htmlspecialchars($row["Owner"], ENT_QUOTES);
									$Editors=htmlspecialchars($row["Editors"], ENT_QUOTES);
								
									echo "<tr class='assessmentrow'>";
										echo "<td>";
											if($Verified==1)
											{
												echo "<i class='material-icons pointer' id='verified_$Assessment_ID' style='color:".getSiteColor()."'>verified_user</i>";
												echo "<div class='mdl-tooltip mdl-tooltip--bottom mdl-tooltip--large' data-mdl-for='verified_$Assessment_ID'>District Created Assessment</div>";
											}
										echo "</td>";
										echo "<td>$Title</td>";
										echo "<td class='hide-on-med-and-down'>$Subject</td>";
										echo "<td class='hide-on-med-and-down'>$Grade</td>";
										echo "<td width=30px>";							
	
											echo "<div class='morebutton' style='position:absolute; margin-top:-15px;'>";
												echo "<button id='demo-menu-bottom-left-$Assessment_ID' class='mdl-button mdl-js-button mdl-button--icon mdl-js-ripple-effect mdl-color-text--grey-600'><i class='material-icons'>more_vert</i></button>";
												echo "<ul class='mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect' for='demo-menu-bottom-left-$Assessment_ID'>";
													echo "<li class='mdl-menu__item duplicateassessment' data-assessmentid='$Assessment_ID'><a href='#' class='mdl-color-text--black' style='font-weight:400'>Make a Copy</a></li>";
												echo "</ul>";
											echo "</div>";
	
										echo "</td>";
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
			echo "<div class='row center-align'><div class='col s12'><h6>There are currently no publicly shared assessments</h6></div></div>";
		}
		
	}

?>
		
<script>
			
	//Process the profile form
	$(function()
	{
		
		//Duplicate Assessment
		$(".duplicateassessment").unbind().click(function(event)
		{
			event.preventDefault();
			var AssessmentIDDuplicate = $(this).data('assessmentid');
			$.ajax({
				type: 'POST',
				url: 'modules/<?php echo basename(__DIR__); ?>/assessment_duplicate.php',
				data: { assessmentIDduplicateid : AssessmentIDDuplicate }
			})
			.done(function(response) {
				$(location).attr('href', '#assessments');
			})
		});
				
		$("#myTable").tablesorter();
					
	});
	
				
</script>