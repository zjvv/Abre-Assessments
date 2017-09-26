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
	require_once(dirname(__FILE__) . '/../../configuration.php'); 
	require_once(dirname(__FILE__) . '/../../core/abre_verification.php'); 
	require(dirname(__FILE__) . '/../../core/abre_dbconnect.php'); 
	require(dirname(__FILE__) . '/../../core/abre_functions.php'); 
	require_once('functions.php');	
	require_once('permissions.php');

	$Assessment_ID=htmlspecialchars($_GET["id"], ENT_QUOTES);
	$Session_ID=htmlspecialchars($_GET["sessionid"], ENT_QUOTES);
		
	$token=getCerticaToken();
	
	?>
	<link rel="stylesheet" href='https://cdn.certicasolutions.com/player/css/player.itemconnect.min.css'>
	<?php
		
	//Mark as started the assessment (if not already marked)
	$sqlstartsession = "SELECT * FROM assessments_status where User='".$_SESSION['useremail']."' and Assessment_ID='$Assessment_ID'";
	$resultstartsession = $db->query($sqlstartsession);
	$sessionstart=mysqli_num_rows($resultstartsession);
	if($sessionstart!=1)
	{
		//Check if Assessment actually exists
		$sqlstartecheck = "SELECT * FROM assessments where ID='$Assessment_ID' and Session_ID='$Session_ID'";
		$resultstartcheck = $db->query($sqlstartecheck);
		$sessionstartcheck=mysqli_num_rows($resultstartcheck);
		if($sessionstartcheck!=0)
		{
			$currenttime=date('Y-m-d H:i:s');
			$stmt = $db->stmt_init();
			$sql = "INSERT INTO assessments_status (Assessment_ID, User, Start_Time) VALUES ('$Assessment_ID', '".$_SESSION['useremail']."', '$currenttime');";
			$stmt->prepare($sql);
			$stmt->execute();
			$stmt->close();
		}
	}
	
	//Find the first question
	$query = "SELECT * FROM assessments_questions where Assessment_ID='$Assessment_ID' order by Question_Order LIMIT 1";
	$dbreturn = databasequery($query);
	$firstquestioncount=count($dbreturn);
	$FirstQuestion=NULL;
	foreach ($dbreturn as $value)
	{
		$FirstQuestion=htmlspecialchars($value["Bank_ID"], ENT_QUOTES);
	}
	
	//Determine if assessment is turned in
	$sqlstartsessionturnin = "SELECT * FROM assessments_status where User='".$_SESSION['useremail']."' and Assessment_ID='$Assessment_ID' and End_Time!='0000-00-00 00:00:00'";
	$resultstartsessionturnin = $db->query($sqlstartsessionturnin);
	$sessionstartturnin=mysqli_num_rows($resultstartsessionturnin);
	if($sessionstartturnin!=0)
	{
		echo "<div class='row center-align'><div class='col s12'><h6>Great job! You have finished the assessment.</h6><i class='material-icons' style='font-size:180px; opacity:.3'>thumb_up</i></div></div>"; 
		
	}
	else
	{
		
		if($firstquestioncount!=0)
		{
		
			//Determine if Session exists
			$sqllookup = "SELECT * FROM assessments where ID='$Assessment_ID' and Session_ID='$Session_ID'";
			$result = $db->query($sqllookup);
			$sessioncount=mysqli_num_rows($result);
			while($row = $result->fetch_assoc())
			{
						
					$Title=htmlspecialchars($row["Title"], ENT_QUOTES);
	
					//Layout
					echo "<div style='position: absolute; top:0; bottom:0; left:0; right:0; overflow-y: hidden;'>";
								
						//List Questions
						echo "<div id='assessmentquestions' style='position: absolute; top:0; bottom:0; width:300px; overflow-y: scroll; background-color:"; echo sitesettings("sitecolor"); echo ";'>";	
							$query = "SELECT * FROM assessments_questions where Assessment_ID='$Assessment_ID' order by Question_Order";
							$dbreturn = databasequery($query);
							$totalquestionsonsession=count($dbreturn);
							$questioncount=0;
							$questionarray=array();
							foreach ($dbreturn as $value)
							{
								$questioncount++;
								$ID=htmlspecialchars($value["ID"], ENT_QUOTES);
								$Bank_ID=htmlspecialchars($value["Bank_ID"], ENT_QUOTES);
								$Points=htmlspecialchars($value["Points"], ENT_QUOTES);
								
								array_push($questionarray, $Bank_ID);
														
								echo "<div class='question pointer' id='questionbutton-$questioncount' data-bankid='$Bank_ID' data-questionnumber='$questioncount' style='padding:2px 30px 2px 30px;'>";
									echo "<div style='float:left; padding:12px 20px 0 0;' id='questionicon-$Bank_ID'>";
									
										//Check to see if already answered
										$queryanswer = "SELECT * FROM assessments_scores where ItemID='$Bank_ID' and User='".$_SESSION['useremail']."' and Assessment_ID='$Assessment_ID'";
										$dbreturnanswer = databasequery($queryanswer);
										$answered=count($dbreturnanswer);
										if($answered==0)
										{
											echo "<i class='material-icons' style='font-size:44px; color:#fff;'>radio_button_unchecked</i>";
										}
										else
										{
											echo "<i class='material-icons questioncomplete' style='font-size:44px; color:#fff;'>radio_button_checked</i>";
										}
										
									echo "</div>";
									echo "<div style='width:220px; color:#fff;'><h6 class='truncate'>Question $questioncount</h6></div>";
								echo "</div>";
							}
						echo "</div>";
					
						//Dashboard Data
						echo "<div id='overview' style='position:absolute; width: calc(100% - 305px); left:305px; top:0; bottom:0; right:0; overflow-y: scroll; padding:20px;'>";
							//echo "<div class='row'>$Title</div>";
							echo "<div id='p2' class='mdl-progress mdl-js-progress mdl-progress__indeterminate landingloader' style='width:100%;'></div>";
							echo "<div class='dashboard'></div>";
						echo "</div>";
						
						$questionarrayjson=json_encode($questionarray);
						echo "<div id='questionarray' data-questionarray='$questionarrayjson'></div>";
										
					echo "</div>";
					
			}
		}
		
		if($sessioncount==0 && $firstquestioncount!=0)
		{
			echo "<div class='row center-align'><div class='col s12'><h6>Whoops...we couldn't find this assessment.</h6><i class='material-icons' style='font-size:180px; opacity:.3'>sentiment_very_dissatisfied</i></div></div>"; 
		}
		else
		{
			include "assessment_student_button.php";
		}
		
		if($firstquestioncount==0)
		{
			echo "<div class='row center-align'><div class='col s12'><h6>Whoops...there are no questions on this assessment.</h6><i class='material-icons' style='font-size:180px; opacity:.3'>sentiment_very_dissatisfied</i></div></div>"; 
		}
		
	}


if($FirstQuestion!=NULL)
{				
	?>
	
	<script>
		
		$(function() {
		
			$.when(
			    $.getScript( "https://cdn.certicasolutions.com/sdk/js/sdk.itemconnect.min.js?x-ic-credential=<?php echo $token; ?>" ),
			    $.getScript( "https://cdn.certicasolutions.com/player/js/player.itemconnect.min.js" ),
			    $.Deferred(function( deferred ){
			        $( deferred.resolve );
			    })
			).done(function()
			{
			
					var QuestionComplete = 0;
					$(".assessmentsubmitbutton").hide();
					var QuestionArray = $('#questionarray').data('questionarray');
					
					<?php if($sessionstartturnin==0 && $sessioncount!=0){ ?>
					
						function CompleteCheck()
						{
							QuestionComplete = $('.questioncomplete').length;			
							if(QuestionComplete===<?php echo $totalquestionsonsession; ?>)
							{
								$(".assessmentsubmitbutton").show();
							}
							else
							{
								$(".assessmentsubmitbutton").hide();
							}				
						}
						
						//Load the first question
						$(".dashboard").load('modules/<?php echo basename(__DIR__); ?>/question_viewer_session.php?id='+<?php echo $FirstQuestion; ?>+'&assessmentid='+<?php echo $Assessment_ID; ?>+'&questionarray='+QuestionArray+'&questionnumber=1', function()
						{ 
							$(".landingloader").hide();
							$(".question:first").css("background-color", "#000");
							$(".question:first").css("color", "#fff");
							CompleteCheck();
						});
						
						//Load the question
						$(document).on("mouseup", ".certicaquestion", function ()
						{
							var QuestionID = $(this).data('questionquestion');
							var QuestionName = '#questionicon-'+QuestionID;
							$(QuestionName).html( "<i class='material-icons' style='font-size:44px; color:#fff;'>radio_button_unchecked</i>" );
							CompleteCheck();
							$('.savebutton').show();
						});
						
						//Hide save button
						$(document).on("click", ".savebutton", function ()
						{
							var QuestionID = $(this).data('scorequestion');
							var NextQuestion = $(this).data('nextquestion');
							var Question_Number = $(this).data('questionnumber');
							var QuestionName = '#questionicon-'+QuestionID;
							$(QuestionName).html( "<i class='material-icons questioncomplete' style='font-size:44px; color:#fff;'>radio_button_checked</i>" );						
							CompleteCheck();			
							$(this).hide();
							
							var TotalQuestionCount=<?php echo $questioncount; ?>
							
							if(Question_Number<TotalQuestionCount)
							{
							
								$(".dashboard").fadeTo(0,0);
								$(".landingloader").show();
								Question_Number_Value=Question_Number+1;
								$(".dashboard").load('modules/<?php echo basename(__DIR__); ?>/question_viewer_session.php?id='+NextQuestion+'&assessmentid='+<?php echo $Assessment_ID; ?>+'&questionarray='+QuestionArray+'&questionnumber='+Question_Number_Value, function()
								{				
									$(".landingloader").hide();
									$('.mdl-layout__content, .dashboard').animate({scrollTop:0}, 0);
									$(".dashboard").fadeTo(0,1);
									var questionbuttondiv = '#questionbutton-'+Question_Number_Value;
									$(".question, #overviewpage").css("background-color", "");
									$(".question, #overviewpage").css("color", "#fff");
									$(questionbuttondiv).css("background-color", "#000");
									$(questionbuttondiv).css("color", "#fff");
								});
								
							}
							
						});

						//Load Question in Window
						$(".question").unbind().click(function()
						{		
							$(".dashboard").fadeTo(0,0);
							$(".landingloader").show();
							$(".question, #overviewpage").css("background-color", "");
							$(".question, #overviewpage").css("color", "#fff");
							$(this).css("background-color", "#000");
							$(this).css("color", "#fff");
							var Bank_ID= $(this).data('bankid');
							var Question_Number= $(this).data('questionnumber');
							CompleteCheck();
							
							$(".dashboard").load('modules/<?php echo basename(__DIR__); ?>/question_viewer_session.php?id='+Bank_ID+'&assessmentid='+<?php echo $Assessment_ID; ?>+'&questionarray='+QuestionArray+'&questionnumber='+Question_Number, function()
							{				
								$(".landingloader").hide();
								$('.mdl-layout__content, .dashboard').animate({scrollTop:0}, 0);
								$(".dashboard").fadeTo(0,1);
							});
						});
						
					<?php } ?>
					
			});
		
		});
	</script>


<script>
	
	//Check Window Width
	if ($(window).width() < 600){ smallView(); }	
	$(window).resize(function(){ if ($(window).width() < 600){ smallView(); } if ($(window).width() >= 600){ largeView(); } });
	function smallView()
	{
		$("#assessmentquestions").css("display", "none");
		$("#overview").css("width", "100%");
		$("#overview").css("left", "0");
	}	
	function largeView()
	{
		$("#assessmentquestions").css("display", "block");
		$("#overview").css("width", "calc(100% - 305px)");
		$("#overview").css("left", "305px");
	}
		
</script>

<?php } ?>