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
		
		$sql = "SELECT * FROM assessments where Owner='".$_SESSION['useremail']."' order by Title";
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
									<th>Title</th>
									<th class='hide-on-med-and-down'>Subject</th>
									<th class='hide-on-med-and-down'>Grade Level</th>
									<th>Class Code</th>
									<th style='width:30px'></th>
								</tr>
							</thead>
							<tbody>
								
								<?php
								$sql = "SELECT * FROM assessments where Owner='".$_SESSION['useremail']."' order by Title";
								$result = $db->query($sql);
								while($row = $result->fetch_assoc())
								{
									$Assessment_ID=htmlspecialchars($row["ID"], ENT_QUOTES);
									$Title=htmlspecialchars($row["Title"], ENT_QUOTES);
									$Description=htmlspecialchars($row["Description"], ENT_QUOTES);
									$Subject=htmlspecialchars($row["Subject"], ENT_QUOTES);
									$Grade=htmlspecialchars($row["Grade"], ENT_QUOTES);
									$Code=htmlspecialchars($row["Code"], ENT_QUOTES);
								
									echo "<tr class='assessmentrow'>";
										echo "<td>$Title</td>";
										echo "<td class='hide-on-med-and-down'>$Subject</td>";
										echo "<td class='hide-on-med-and-down'>$Grade</td>";
										echo "<td>$Code</td>";
										echo "<td width=30px>";							
	
											echo "<div class='morebutton' style='position:absolute; margin-top:-15px;'>";
												echo "<button id='demo-menu-bottom-left-$Assessment_ID' class='mdl-button mdl-js-button mdl-button--icon mdl-js-ripple-effect mdl-color-text--grey-600'><i class='material-icons'>more_vert</i></button>";
												echo "<ul class='mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect' for='demo-menu-bottom-left-$Assessment_ID'>";
													echo "<li class='mdl-menu__item modal-createassessment' href='#createassessment' data-title='$Title' data-description='$Description' data-subject='$Subject' data-assessmentid='$Assessment_ID' data-grade='$Grade' style='font-weight:400'>Edit Assessment</a></li>";
													echo "<li class='mdl-menu__item exploreassessment'><a href='#assessments/$Assessment_ID' class='mdl-color-text--black' style='font-weight:400'>Edit Questions</a></li>";
													echo "<li class='mdl-menu__item deleteassessment'><a href='modules/".basename(__DIR__)."/assessment_delete.php?assessmentid=".$Assessment_ID."' class='mdl-color-text--black' style='font-weight:400'>Delete</a></li>";
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
			echo "<div class='row center-align'><div class='col s12'><h6>You haven't created any assessments</h6></div><div class='col s12'>Click the '+' in the bottom right to create a new assessment.</div></div>";
		}
		
		include "assessment_button.php";
		
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
			var Assessment_Grade = $(this).data('grade');
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
			var result = confirm("Delete this assessment?");
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