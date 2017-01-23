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
		
	//Get Token
	$token=getCerticaToken();	
		
	echo "<div class='row' style='padding:15px;'>";
		echo "<div id='passage-content-$questionid'></div>";
		echo "<div class='certicaquestion' id='content-element-$questionid'></div>";
		echo "<div id='rubric-content-$questionid'></div>";
	echo "</div>";
		
	echo "<div class='row' style='padding:0 15px 0 15px'><span id='btn-score-$questionid' class='modal-close waves-effect btn-flat white-text' style='background-color: "; echo sitesettings("sitecolor"); echo "'>Save</span></div>";
	echo "<div class='row' style='padding:0 15px 0 15px'><div id='pnl-score-$questionid'></div></div>";
		
?>
		
		
		<script>
			
			$(function()
			{
				
				var lastSubmitted = {};
		
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
		            
		            //Show Feedback
		            if(value_score==="1")
		            { 
			            score='Correct'; 
			            $('#pnl-score-<?php echo $questionid ?>').html('<div class="card white-text" style="background-color:#4CAF50; padding:20px;">'+score+'</div>');
			        }
			        if(value_score==="0")
		            {
				        score='Incorrect';
				        $('#pnl-score-<?php echo $questionid ?>').html('<div class="card white-text" style="background-color:#F44336; padding:20px;">'+score+'</div>');
				    }
				    if(value_score==="")
		            {
				        score='Rubric Graded';
				        $('#pnl-score-<?php echo $questionid ?>').html('<div class="card black-text" style="background-color:#FFEB3B; padding:20px;">'+score+'</div>');
				    }
		            
		            //Save the result
		            $.post("modules/<?php echo basename(__DIR__); ?>/session_scoring.php", { score: value_score, scoredOn: value_scoredOn, itemId: value_itemId, itemResponse: value_itemResponse, scoreGUID: value_scoreGUID  });
		           
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
		        
		        //Hydrate Last Response
		        $('#btn-restore-<?php echo $questionid ?>').unbind().click(function ()
		        {
               		$('#content-element-<?php echo $questionid ?>').player('selection', lastSubmitted);
            	});
		
			});
		
		</script>