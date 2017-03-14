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
	
	$Assessment_ID=htmlspecialchars($_GET["id"], ENT_QUOTES);
	$Session_ID=htmlspecialchars($_GET["sessionid"], ENT_QUOTES);
		
	$token=getCerticaToken();
	
	?>
	<script src='https://cdn.certicasolutions.com/sdk/js/sdk.itemconnect.min.js?x-ic-credential=<?php echo $token; ?>'></script>
	<script src='https://cdn.certicasolutions.com/player/js/player.itemconnect.min.js'></script>
	<link rel="stylesheet" href='https://cdn.certicasolutions.com/player/css/player.itemconnect.min.css'>
	<?php
		
	//Mark as started the assessment (if not already marked)
	$sqlstartsession = "SELECT * FROM assessments_status where User='".$_SESSION['useremail']."' and Assessment_ID='$Assessment_ID'";
	$resultstartsession = $db->query($sqlstartsession);
	$sessionstart=mysqli_num_rows($resultstartsession);
	if($sessionstart!=1)
	{
		$currenttime=date('Y-m-d H:i:s');
		$stmt = $db->stmt_init();
		$sql = "INSERT INTO assessments_status (Assessment_ID, User, Start_Time) VALUES ('$Assessment_ID', '".$_SESSION['useremail']."', '$currenttime');";
		$stmt->prepare($sql);
		$stmt->execute();
		$stmt->close();
	}
	
	//Determine if assessment is turned in
	$sqlstartsessionturnin = "SELECT * FROM assessments_status where User='".$_SESSION['useremail']."' and Assessment_ID='$Assessment_ID' and End_Time!='0000-00-00 00:00:00'";
	$resultstartsessionturnin = $db->query($sqlstartsessionturnin);
	$sessionstartturnin=mysqli_num_rows($resultstartsessionturnin);
	if($sessionstartturnin!=0)
	{
		echo "<div class='row center-align'><div class='col s12'><h6>Assessment Complete</h6></div></div>"; 
		
	}
	else
	{

		//Determine if Session exists
		$sqllookup = "SELECT * FROM assessments where ID='$Assessment_ID' and Session_ID='$Session_ID'";
		$result = $db->query($sqllookup);
		$sessioncount=mysqli_num_rows($result);
		while($row = $result->fetch_assoc())
		{
					
				$Title=htmlspecialchars($row["Title"], ENT_QUOTES);
		
				echo "<div class='page_container'>";
					echo "<div class='row'><div class='center-align' style='padding:20px;'><h3 style='font-weight:600;'>$Title</h3></div></div>";
					echo "<ul class='collapsible popout questionsort' data-collapsible='accordion'>";
					
						$sqllookup2 = "SELECT * FROM assessments_questions where Assessment_ID='$Assessment_ID' order by Question_Order";
						$result3 = $db->query($sqllookup2);
						$unitcount=mysqli_num_rows($result3);
						$questioncount=0;
						while($row2 = $result3->fetch_assoc())
						{
							$questioncount++;
							$questionid=htmlspecialchars($row2["ID"], ENT_QUOTES);
							$Bank_ID=htmlspecialchars($row2["Bank_ID"], ENT_QUOTES);
							$Points=htmlspecialchars($row2["Points"], ENT_QUOTES);
							if($Points==""){ $Points=0; }
		
							echo "<li style='position:relative' id='item-$questionid' class='topicholder'>";
								echo "<div class='collapsible-header unit' data-bankid='$Bank_ID'>";
							    	echo "<i class='material-icons mdl-color-text--red dot' style='font-size: 36px;'>fiber_manual_record</i>";
									echo "<span style='position:absolute; right:0; z-index:1000; cursor:move;' class='mdl-color-text--grey-700'></span>";	
									echo "<span class='title truncate mdl-color-text--red dottext' style='margin-right:40px;'><b>Question $questioncount</b></span>";
								echo "</div>";
									
								echo "<div class='collapsible-body mdl-color--white' style='padding:25px'>";
									echo "<div id='questionplayerloader-$Bank_ID' style='display:none;'><div id='p2' class='mdl-progress mdl-js-progress mdl-progress__indeterminate' style='width:100%'></div></div>";
									echo "<div id='questionplayer-$Bank_ID' class='questionholder'></div>";	
								echo "</div>";
							echo "</li>";
						
						}
		
					echo "</ul>";
				echo "</div>";
	
		
		}
		
		if($sessioncount==0)
		{
			echo "<div class='row center-align'><div class='col s12'><h6>Invalid Assessment</h6></div></div>"; 
		}
		else
		{
			include "assessment_student_button.php";
		}
		
	}
		

?>
	
	<script>
		
		$(function()
		{

			//Call accordion
			$('.collapsible').collapsible({ });			
			
			//Load the question
			$(".questionholder").unbind().click(function(event)
			{
				$(this).last('div').find('.savebutton').show();
				$(this).closest("li").find(".dot").removeClass("mdl-color-text--green");
				$(this).closest("li").find(".dottext").removeClass("mdl-color-text--green");
			});
			
			//Hide save button
			$(document).on("click", ".savebutton", function ()
			{
				$(this).hide();
				$(this).closest("li").find(".dot").addClass("mdl-color-text--green");
				$(this).closest("li").find(".dottext").addClass("mdl-color-text--green");
			});
 			
			//Load the question
			$(".collapsible-header").unbind().click(function(event)
			{
				var Bank_ID= $(this).data('bankid');
				var timeout = setTimeout(function(){ $('#questionplayerloader-'+Bank_ID).show(); }, 1000);
 				$('#questionplayer-'+Bank_ID).hide();
 				$('.toolbar').hide();
 				
 				$.get( "modules/<?php echo basename(__DIR__); ?>/question_viewer_session.php", { id: Bank_ID, assessmentid: <?php echo $Assessment_ID; ?> } )
			    .done(function( data ) {
				    clearTimeout(timeout);
				    $('#questionplayerloader-'+Bank_ID).hide();
				    $('#questionplayer-'+Bank_ID).show();
				    $('.toolbar').show();
			    	$("#questionplayer-"+Bank_ID).html( data );
			  	});
			  	
 			});
			
		});
		
	</script>