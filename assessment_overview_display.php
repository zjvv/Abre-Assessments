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
							
							//Loop through each assessment question
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
									echo "<table id='myTable' class='tablesorter'>";
									echo "<thead><tr class='pointer'><th>Question</th><th>ID</th><th>Standard</th><th>Type</th><th class='center-align'>Difficulty</th><th class='center-align'>Points</th><th></th></tr></thead>";
									echo "<tbody>";
								}
								
								$Bank_ID=htmlspecialchars($row["Bank_ID"], ENT_QUOTES);
								$Points=htmlspecialchars($row["Points"], ENT_QUOTES);
								$Vendor_ID=htmlspecialchars($row["Vendor_ID"], ENT_QUOTES);
								$type=htmlspecialchars($row["Type"], ENT_QUOTES);
								$difficulty=htmlspecialchars($row["Difficulty"], ENT_QUOTES);
								$standard=htmlspecialchars($row["Standard"], ENT_QUOTES);
								$Standard_Text = str_replace("CCSS.Math.Content.","",$standard);
								$Standard_Text = str_replace("CCSS.ELA-Literacy.","",$Standard_Text);
								if($Points==""){ $Points=0; }
									
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
								
								echo "<tr>";
									echo "<td>$questioncount</td>";
									echo "<td>$Vendor_ID</td>";
									echo "<td>$Standard_Text</td>";
									echo "<td>$type</td>";
									echo "<td bgcolor='$questioncolor' class='center-align'>$difficulty</td>";
									echo "<td class='center-align'>$Points</td>";
									echo "<td width='30px'><button class='mdl-button mdl-js-button mdl-button--icon mdl-js-ripple-effect mdl-color-text--grey-600 previewquestion pointer' data-question='$Bank_ID'><i class='material-icons'>visibility</i></button></td>";
								echo "</tr>";
							}
							
							if($numrows==$questioncount)
							{
								echo "</tbody>";
								echo "</table>";
								echo "</div></div>";
								echo "</div></div>";
							}
							
							if($numrows==0)
							{
								echo "<div class='row center-align'><div class='col s12'><h6>No questions have been added.</h6></div><div class='col s12'>Click the 'Questions' at the top to add a question.</div></div>";
							}

		
		
	}

?>

<script>
				
	$(function()
	{
			
		//Preview the assessment question
		$( ".previewquestion" ).unbind().click(function() {
						
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
		
		$("#myTable").tablesorter();
		
					
	});			
				
</script>