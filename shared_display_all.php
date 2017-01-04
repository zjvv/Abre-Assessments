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
												echo "<i class='material-icons pointer' id='verified_$Assessment_ID' style='color:".sitesettings("sitecolor")."'>verified_user</i>";
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
													echo "<li class='mdl-menu__item exploreassessment'><a href='#assessments/$Assessment_ID' class='mdl-color-text--black' style='font-weight:400'>Preview</a></li>";
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
			echo "<div class='row center-align'><div class='col s12'><h6>There are currently no shared assessments</h6></div></div>";
		}
		
	}

?>
		
<script>
			
	//Process the profile form
	$(function()
	{
		
		//Make Explore clickable
		$(".exploreassessment").unbind().click(function() {
			 window.open($(this).find("a").attr("href"), '_self');
		});
		
		$(document).on("click", ".modal-createassessment", function () {
			var Assessment_ID = $(this).data('assessmentid');
			$(".modal-content #assessment_id").val(Assessment_ID);
			var Assessment_Title = $(this).data('title');
			$(".modal-content #assessment_title").val(Assessment_Title);
			var Assessment_Description = $(this).data('description');
			$(".modal-content #assessment_description").val(Assessment_Description);
			var Assessment_Editors = $(this).data('editors');
			$(".modal-content #assessment_editors").val(Assessment_Editors);
			var Assessment_Grade = $(this).data('grade');
			var Assessment_shared = $(this).data('shared');
			if(Assessment_shared=='1')
			{
				$(".modal-content #assessment_share").prop('checked',true);
			}
			else
			{
				$(".modal-content #assessment_share").prop('checked',false);
			}
			var Assessment_Locked = $(this).data('locked');
			if(Assessment_Locked=='1')
			{
				$(".modal-content #assessment_lock").prop('checked',true);
			}
			else
			{
				$(".modal-content #assessment_lock").prop('checked',false);
			}
			if(Assessment_Grade!="blank")
			{
				var Assessment_Grade_String=String(Assessment_Grade);
				if( Assessment_Grade_String.indexOf(',') >= 0)
				{
					var dataarrayassessment=Assessment_Grade.split(", ");
					$("#assessment_grade").val(dataarrayassessment);
				}
				else
				{
					$("#assessment_grade").val(Assessment_Grade_String);
				}
			}
			else
			{
				$("#assessment_grade").val('');
			}
			var Assessment_Subject = $(this).data('subject');
			if(Assessment_Subject!="blank")
			{
				$("#assessment_subject option[value='"+Assessment_Subject+"']").prop('selected',true);
			}
			else
			{
				$("#assessment_subject option[value='']").prop('selected',true);
			}
		});	
		
		//Delete assessment
		$( ".deleteassessment" ).unbind().click(function() {
			event.preventDefault();
			var result = confirm("Are you sure you want to delete this assessment?");
			if (result) {

				//Make the post request
				var address = $(this).find("a").attr("href");
				$.ajax({
					type: 'POST',
					url: address,
					data: '',
				})
																
				//Show the notification
				.done(function(response){	
					
					mdlregister();												
					var notification = document.querySelector('.mdl-js-snackbar');
					var data = { message: response };
					notification.MaterialSnackbar.showSnackbar(data);
					
					$('#content_holder').load('modules/<?php echo basename(__DIR__); ?>/assessments_display_all.php', function() { init_page(); });
						
				})
			}
		});	
				
		$("#myTable").tablesorter();
					
	});
	
				
</script>