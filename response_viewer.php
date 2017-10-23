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
	require_once('functions.php');
	require_once('permissions.php');
	
	if($pagerestrictions=="")
	{
	
		$questionid=$_GET["id"];
		$assessmentid=$_GET["assessmentid"];
		$user=$_GET["user"];
		
		//Get Token
		$token=getCerticaToken();	
		
		echo "<div class='row' style='padding:15px;'>";
			echo "<div id='passage-content-$questionid'></div>";
			echo "<div class='certicaquestion' id='content-element-$questionid'></div>";
			echo "<div id='rubric-content-$questionid'></div>";
			
			//Find the Type of Question
			$sql = "SELECT * FROM assessments_questions where Assessment_ID='$assessmentid' and Bank_ID='$questionid'";
			$result = $db->query($sql);
			while($row = $result->fetch_assoc())
			{
				$Points=htmlspecialchars($row["Points"], ENT_QUOTES);
				if($Points==""){ $Points="1"; }
				$QuestionType=htmlspecialchars($row["Type"], ENT_QUOTES);
				if($QuestionType=="Open Response")
				{
								
					//Check to see if there is existing entered
					$CurrentScore="";
					$sql2 = "SELECT * FROM assessments_scores where Assessment_ID='$assessmentid' and ItemID='$questionid' and User='$user'";
					$result2 = $db->query($sql2);
					while($row2 = $result2->fetch_assoc())
					{
						$CurrentScore=htmlspecialchars($row2["Score"], ENT_QUOTES);
						if($CurrentScore==""){ $CurrentScore=""; }
					}
					
					$Username=str_replace("@","",$user);
					$Username=str_replace(".","",$Username);
					echo "<div><b>Teacher Entered Score (Points Possible: $Points - Enter Points Based on Rubric):</b><input type='number' class='teacherrubricscore' data-itemid='$questionid' data-assessmentid='$assessmentid' data-user='$user' data-userclean='$Username' value='$CurrentScore' min='0'></div>";
				}
			}
				
		echo "</div>";
		
		?>
		
		
		<script>
			
			$(function()
			{
				
				//Check if object is empty
				function isEmpty(obj) {
				    for(var key in obj) {
				        if(obj.hasOwnProperty(key))
				            return false;
				    }
				    return true;
				}
				
				//Get last submitted response from this user
				lastSubmittedresponse = {};
				<?php
				$query = "SELECT * FROM assessments_scores where User='$user' and ItemID='$questionid' and Assessment_ID='$assessmentid'";
				$dbreturn = databasequery($query);
				foreach ($dbreturn as $value)
				{
					$Response=$value["Response"];
				?>
					var lastSubmittedresponse = <?php echo $Response; ?>;
					$('#content-element-<?php echo $questionid ?>').player('selection', lastSubmittedresponse);
				<?php
				}
				?>
		
				//Display the Question Player
				ItemConnect.content.get({
			        id: <?php echo $questionid; ?>,
			        token: "<?php echo $token; ?>",
			        contentTypeFlags: 15,
			        onSuccess: function (data) {
			            $('#content-element-<?php echo $questionid ?>').html(data);
			            $('#content-element-<?php echo $questionid ?>').player({
	                        useMathML: true,
	                        enablePassages: true,
	                        passageContainer: '#passage-content-<?php echo $questionid ?>',
	                        enableRubric: true,
	                        rubricContainer: '#rubric-content-<?php echo $questionid ?>'
			            });
			            //Hydrate Last Response from User
			            if(!isEmpty(lastSubmittedresponse))
			            {
			            	$('#content-element-<?php echo $questionid ?>').player('selection', lastSubmittedresponse);
			            }
					}
				});
				
				//Update points
				$( ".teacherrubricscore" ).change(function()
				{			
					//Save Points
					var rubricquestionvalue = $(this).val();
					var itemid = $(this).data("itemid");
					var assessmentid = $(this).data("assessmentid");
					var user = $(this).data("user");
					var userclean = $(this).data("userclean");
					$.post( "/modules/<?php echo basename(__DIR__); ?>/rubric_savepoints.php", { itemid: itemid, rubricquestionvalue: rubricquestionvalue, assessmentid: assessmentid, user: user })
					.done(function( data ) {
						$('#rubric-total-'+userclean).html(data.RubricPoints);
						$('#score-total-'+userclean).html(data.Score);
						$('#percentage-total-'+userclean).html(data.Percentage);
						$('#rubric-question-'+userclean+'-'+itemid).css('background-color', '#1565C0');
						$('#rubric-question-'+userclean+'-'+itemid).html('<i class="material-icons" style="color:#0D47A1">star</i>');
					});
					
				});
		
			});
		
		</script>
		
<?php
	}
		
?>