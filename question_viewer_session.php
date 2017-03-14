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
	require_once(dirname(__FILE__) . '/../../core/abre_dbconnect.php');
	require_once(dirname(__FILE__) . '/../../core/abre_functions.php');
	require_once('functions.php');
	require_once('permissions.php');
	
	if(!isset($Bank_ID)){ $questionid=$_GET["id"]; }else{ $questionid=$Bank_ID; }
	$assessmentid=$_GET["assessmentid"];
	$questionarray=$_GET["questionarray"];
	$questionnumber=$_GET["questionnumber"];
	

	$myArray = explode(',', $questionarray);
	$gotoquestion=$myArray[$questionnumber];
		
	//Get Token
	$token=getCerticaToken();	
		
	echo "<div class='mdl-shadow--2dp' style='background-color:#fff; padding:20px 40px 40px 40px'>";
		echo "<div class='row' style='padding:15px;'>";
			echo "<div id='passage-content-$questionid'></div>";
			echo "<div class='certicaquestion' id='content-element-$questionid' data-questionquestion='$questionid' style='margin-right:70px;'></div>";
		echo "</div>";
		echo "<div class='row' style='padding:0 15px 0 15px'>";
			echo "<span id='btn-score-$questionid' data-scorequestion='$questionid' data-nextquestion='$gotoquestion' data-questionnumber='$questionnumber' class='waves-effect btn-flat savebutton white-text' style='display:none; background-color:"; echo sitesettings("sitecolor"); echo "'>Save</span>";
		echo "</div>";
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
				$query = "SELECT * FROM assessments_scores where User='".$_SESSION['useremail']."' and ItemID='$questionid' and Assessment_ID='$assessmentid'";
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
			        onSuccess: function (data) {
			            $('#content-element-<?php echo $questionid ?>').html(data);
			            $('#content-element-<?php echo $questionid ?>').player({
			                useMathML: true,
			                enablePassages: true,
			                passageContainer: '#passage-content-<?php echo $questionid ?>',
			                enableRubric: false,
			                rubricContainer: '#rubric-content-<?php echo $questionid ?>'
			            });
			            //Hydrate Last Response from User
			            if(!isEmpty(lastSubmittedresponse))
			            {
			            	$('#content-element-<?php echo $questionid ?>').player('selection', lastSubmittedresponse);
			            }
					}
				});
				
				//Handle the response
				function formatResponseData(response)
				{	
		            lastSubmitted = JSON.parse(response.scores[0].response);
		            var value_score = response['scores'][0]['score'];
		            var value_scoredOn = response['scores'][0]['scoredOn'];
		            var value_itemId = response['scores'][0]['iA_ItemId'];
		            var value_itemResponse = response['scores'][0]['response'];
		            var value_scoreGUID = response['scores'][0]['scoreGUID'];
		            
		            //Save the result
		            $.post("modules/<?php echo basename(__DIR__); ?>/session_scoring.php", { assessmentid: <?php echo $assessmentid; ?>, score: value_score, scoredOn: value_scoredOn, itemId: value_itemId, itemResponse: value_itemResponse, scoreGUID: value_scoreGUID  });
		           
		        }
		        
		        //Error Handle
		        function handleError(ex)
		        {
		            
		        }

				//Score the Question
				$('#btn-score-<?php echo $questionid ?>').unbind().click(function () 
				{
		            //Get current selection from player
		            var selection = $('#content-element-<?php echo $questionid ?>').player('selection');
		
		            //Get the item id from the item metadata function of the player
		            var itemID = $('#content-element-<?php echo $questionid ?>').player('metadata', 'item');
		
		            selection.cF_1 = 'custom data 1';
		            selection.cF_2 = 'custom data 2';
		            selection.cF_3 = 'custom data 3';
		            selection.cF_4 = 'custom data 4';
		
		            ItemConnect.responses.create({
		                id: itemID,
		                token: "<?php echo $token; ?>",
		                response: selection,
		                onSuccess: function (location, cxt) {
		                    ItemConnect.responses.get({
		                        id: location,
		                        token: "<?php echo $token; ?>",
		                        onSuccess: formatResponseData,
		                        onFailure: handleError
		                    });
		                },
		                onFailure: handleError
		            });
		        });
		
			});
		
		</script>