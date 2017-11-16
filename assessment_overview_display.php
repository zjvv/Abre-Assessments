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
	require(dirname(__FILE__) . '/../../configuration.php');
	require_once(dirname(__FILE__) . '/../../core/abre_functions.php');
	require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
	require_once('functions.php');
	require_once('permissions.php');
	
	if($pagerestrictions=="")
	{
		
		$token=getCerticaToken();
		
		?>
		<script src='https://cdn.certicasolutions.com/sdk/js/sdk.itemconnect.min.js?x-ic-credential=<?php echo $token; ?>'></script>
		<script src='https://cdn.certicasolutions.com/player/js/player.itemconnect.min.js'></script>
		<link rel="stylesheet" href='https://cdn.certicasolutions.com/player/css/player.itemconnect.min.css'>	
		<?php

		$Assessment_ID=htmlspecialchars($_GET["id"], ENT_QUOTES);
		
		//Find Out Level of Assessment and if Assessment is a District Assessment
		$sql = "SELECT * FROM assessments where ID='$Assessment_ID'";
		$result = $db->query($sql);
		while($row = $result->fetch_assoc())
		{
			$assessmentlevel=htmlspecialchars($row["Level"], ENT_QUOTES);
			$assessmentverified=htmlspecialchars($row["Verified"], ENT_QUOTES);
			$Owner=htmlspecialchars($row["Owner"], ENT_QUOTES);
			$Editors=htmlspecialchars($row["Editors"], ENT_QUOTES);
			$Locked=htmlspecialchars($row["Locked"], ENT_QUOTES);
			
			//Check to see if allowed to edit
			$access=0;
			if($Owner==$_SESSION['useremail']){ $access=1; }
			if(strpos($Editors, $_SESSION['useremail']) !== false){ $access=1; }
			
			if($access!=0)
			{
				if($assessmentlevel=="Core"){ $totallowlevel=7; $totalmediumlevel=10; $totalhighlevel=3; }
				if($assessmentlevel=="College Preparatory"){ $totallowlevel=5; $totalmediumlevel=10; $totalhighlevel=5; }
				if($assessmentlevel=="Honors"){ $totallowlevel=3; $totalmediumlevel=10; $totalhighlevel=7; }
				
				if($assessmentverified==1)
				{
					
					//Check to see how many questions of each level
					$sql = "SELECT * FROM assessments_questions where Assessment_ID='$Assessment_ID'";
					$result = $db->query($sql);
					$numoflow=0;
					$numofhigh=0;
					$numofmedium=0;
					while($row = $result->fetch_assoc())
					{
						$difficulty=htmlspecialchars($row["Difficulty"], ENT_QUOTES);
						if($difficulty=="Low"){ $numoflow++; }
						if($difficulty=="High"){ $numofhigh++; }
						if($difficulty=="Medium"){ $numofmedium++; }
					}
					
					//Show Overview
					echo "<div class='page_container'><div class='row' style='margin:0 -12px 20px -12px;'>";
							
						//Overview cards
						echo "<div class='col m4 s12'><div class='mdl-card mdl-shadow--2dp' style='width:100%; color:#fff; padding-top:45px; background-color:"; echo getSiteColor(); echo "'>";
							if($numoflow==$totallowlevel)
							{
								echo "<span class='center-align truncate'><i class='material-icons' style='font-size:70px; line-height:80px;'>check_circle</i></span>";
								echo "<span class='center-align truncate'>$numoflow Low Questions</span>";
							}
							else
							{
								echo "<span class='center-align truncate' style='font-size:70px; line-height:80px;'>$numoflow</span>";
								echo "<span class='center-align truncate'>of $totallowlevel Low Questions</span>";
							}
						echo "</div></div>";
						echo "<div class='col m4 s12'><div class='mdl-card mdl-shadow--2dp' style='width:100%; color:#fff; padding-top:45px; background-color:"; echo getSiteColor(); echo "'>";
							if($numofmedium==$totalmediumlevel)
							{
								echo "<span class='center-align truncate'><i class='material-icons' style='font-size:70px; line-height:80px;'>check_circle</i></span>";
								echo "<span class='center-align truncate'>$numofmedium Medium Questions</span>";
							}
							else
							{
								echo "<span class='center-align truncate' style='font-size:70px; line-height:80px;'>$numofmedium</span>";
								echo "<span class='center-align truncate'>of $totalmediumlevel Medium Questions</span>";
							}
						echo "</div></div>";
						echo "<div class='col m4 s12'><div class='mdl-card mdl-shadow--2dp' style='width:100%; color:#fff; padding-top:45px; background-color:"; echo getSiteColor(); echo "'>";
							if($numofhigh==$totalhighlevel)
							{
								echo "<span class='center-align truncate'><i class='material-icons' style='font-size:70px; line-height:80px;'>check_circle</i></span>";
								echo "<span class='center-align truncate'>$numofhigh High Questions</span>";
							}
							else
							{
								echo "<span class='center-align truncate' style='font-size:70px; line-height:80px;'>$numofhigh</span>";
								echo "<span class='center-align truncate'>of $totalhighlevel High Questions</span>";
							}
						echo "</div></div>";
					
					echo "</div></div>";
					
				}
			}
		}
							
		//Loop through each assessment question
		if($access!=0)
		{
			$sql = "SELECT * FROM assessments_questions where Assessment_ID='$Assessment_ID' order by Question_Order";
			$result = $db->query($sql);
			$numrows = $result->num_rows;
			$questioncount=0;
			while($row = $result->fetch_assoc())
			{
				$questioncount++;
									
				if($questioncount==1)
				{
					echo "<div class='page_container mdl-shadow--4dp'>";
					echo "<div class='page'>";
					echo "<div class='row'><div class='col s12'>";
					echo "<table class='tablesorter bordered' id='sort'>";
					echo "<thead><tr class='pointer'>";
					echo "<th>Question</th>";
					echo "<th class='hide-on-med-and-down'>ID</th>";
					echo "<th class='hide-on-med-and-down'>Standard</th>";
					echo "<th class='hide-on-med-and-down'>Type</th>";
					echo "<th class='center-align'>Difficulty</th>";
					echo "<th class='center-align hide-on-small-only'>Points</th>";
					if($Locked!=1 && $access==1)
					{
						echo "<th></th>";
						echo "<th></th>";
						echo "<th class='hide-on-small-only'></th>";
					}
					echo "</tr></thead>";
					echo "<tbody>";
				}
									
				$Bank_ID=htmlspecialchars($row["Bank_ID"], ENT_QUOTES);
				$Points=htmlspecialchars($row["Points"], ENT_QUOTES);
				$questionid=htmlspecialchars($row["ID"], ENT_QUOTES);
				$Vendor_ID=htmlspecialchars($row["Vendor_ID"], ENT_QUOTES);
				$type=htmlspecialchars($row["Type"], ENT_QUOTES);
				$difficulty=htmlspecialchars($row["Difficulty"], ENT_QUOTES);
				$standard=htmlspecialchars($row["Standard"], ENT_QUOTES);
				$Standard_Text = str_replace("CCSS.Math.Content.","",$standard);
				$Standard_Text = str_replace("CCSS.ELA-Literacy.","",$Standard_Text);
				if($Standard_Text==""){ $Standard_Text="No Linked Standard"; }
				if($Points==""){ $Points=1; }
										
				//Type
				if($type=="MC"){ $type="Multiple Choice"; }
				if($type=="CM"){ $type="Choice Multiple"; }
				if($type=="GM"){ $type="Gap Match"; }
				if($type=="GR"){ $type="Graphic Gap Match"; }
				if($type=="HS"){ $type="Hot Spot"; }
				if($type=="HT"){ $type="Hot Text"; }
				if($type=="IC"){ $type="Inline Choice"; }
				if($type=="OD"){ $type="Order"; }
				if($type=="OR"){ $type="Open Response"; }
				if($type=="TE"){ $type="Text Entry"; }
										
				//Color
				if($difficulty=="Low"){ $questioncolor='#F44336'; }
				if($difficulty=="Medium"){ $questioncolor='#FFEB3B'; }
				if($difficulty=="High"){ $questioncolor='#4CAF50'; }
									
				echo "<tr id='item-$questionid' style='background-color:#fff'>";
					echo "<td width='100px'><span class='index'>$questioncount</span></td>";
					echo "<td class='hide-on-med-and-down'>$Vendor_ID</td>";
					echo "<td class='hide-on-med-and-down'>$Standard_Text</td>";
					echo "<td class='hide-on-med-and-down'>$type</td>";
					echo "<td bgcolor='$questioncolor' class='center-align'>$difficulty</td>";
					if($Locked!=1 && $access==1)
					{
						echo "<td width='100px' class='center-align hide-on-small-only'><input class='questionpoints' id='$questionid' type='number' min='1' style='text-align:center; width:50px;' value='$Points'></td>";
					}
					else
					{
						echo "<td class='center-align' class='hide-on-small-only'>$Points</td>";
					}
					echo "<td width='30px'><button class='mdl-button mdl-js-button mdl-button--icon mdl-js-ripple-effect mdl-color-text--grey-600 previewquestion' data-question='$Bank_ID'><i class='material-icons'>visibility</i></button></td>";
					if($Locked!=1 && $access==1)
					{
						echo "<td width='30px'><a href='modules/".basename(__DIR__)."/question_remove_process.php?questionid=".$questionid."' class='mdl-button mdl-js-button mdl-button--icon mdl-js-ripple-effect mdl-color-text--grey-600 removequestion'><i class='material-icons'>delete</i></button></td>";
						echo "<td width='30px' class='hide-on-small-only'><span class='mdl-button mdl-js-button mdl-button--icon mdl-color-text--grey-600 handle' data-question='$Bank_ID'><i class='material-icons'>reorder</i></button></td>";
					}
				echo "</tr>";
			}
		}
							
		if($access!=0)
		{
			if($numrows==$questioncount)
			{
				echo "</tbody>";
				echo "</table>";
				echo "</div></div>";
				echo "</div></div>";
			}
								
			if($numrows==0)
			{
				echo "<div class='row center-align'><div class='col s12'><h6>No questions have been added.</h6></div><div class='col s12'>Click the '+' in the bottom right to add a question to this assessment.</div></div>";
			}
								
			if($Locked!=1 && $access==1){ include "question_button.php"; }
		}
		else
		{
			echo "<div class='row center-align'><div class='col s12'><h6>You do not have overview access to this assessment.</h6></div></div>";
		}
		
		
	}

?>

<script>
				
	$(function()
	{
			
		//Preview the assessment question
		$( ".previewquestion" ).unbind().click(function()
		{				
			event.preventDefault();		
			$(".addquestiontoassessmentpreview").css("display", "none");		
			var Question = $(this).data('question');
			$('#previewmeta').hide();
			
			$(".modal-content #questionholder").load( "modules/<?php echo basename(__DIR__); ?>/question_viewer.php?id="+Question, function(){
			});
						
			$('#linktotopic').openModal({
				in_duration: 0,
				out_duration: 0,
			});
		});
		
		//Question sorting
		updateIndex = function(e, ui)
		{
			$('.index', ui.item.parent()).each(function (i) {
			    $(this).html(i + 1);
			});
		};			
		var fixHelper = function(e, ui) {  
		ui.children().each(function() {  
			$(this).width($(this).width());  
		});  
		return ui;  
		};
		$( "#sort tbody" ).sortable({
			axis: 'y',
			handle: '.handle',
			helper: fixHelper,
			stop: updateIndex,
			update: function(event, ui){
					
				//Sent Form Data
				var data = $(this).sortable('serialize');
				$.ajax({
			        data: data,
			        type: 'POST',
			        url: '/modules/<?php echo basename(__DIR__); ?>/questions_save_order.php'
			    });
			        
			}
		});
			
		//Remove topic from assessment
		$( ".removequestion" ).click(function()
		{
			event.preventDefault();
			var result = confirm("Remove this question?");
			if (result) {
				$(this).closest(".topicholder").hide();
				var address = $(this).attr("href");
				$.ajax({
					type: 'POST',
					url: address,
					data: '',
				})
																	
				//Show the notification
				.done(function(response) {	
					$("#content_holder").load( "modules/<?php echo basename(__DIR__); ?>/assessment_overview.php?id="+<?php echo $Assessment_ID; ?>, function(){
						mdlregister();
							
						var notification = document.querySelector('.mdl-js-snackbar');
						var data = { message: response };
						notification.MaterialSnackbar.showSnackbar(data);
							
					});
				})
					
			}
		});
			
		//Update points
		$( ".questionpoints" ).change(function()
		{			
			//Save Points
			var questionid = $(this).attr('id');
			var questionvalue = $(this).val();
			$.post( "/modules/<?php echo basename(__DIR__); ?>/question_savepoints.php", { questionid: questionid, questionvalue: questionvalue })	
		});
		
		$("#sort").tablesorter();
					
	});			
				
</script>