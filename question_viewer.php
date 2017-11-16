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
	
		if(!isset($Bank_ID)){ $questionid=$_GET["id"]; }else{ $questionid=$Bank_ID; }
		
		//Get Token
		$token=getCerticaToken();	
		
		echo "<div class='row' style='padding:15px;'>";
			echo "<div id='passage-content-$questionid'></div>";
			echo "<div class='certicaquestion' id='content-element-$questionid'></div>";
			echo "<div id='rubric-content-$questionid'></div>";
		echo "</div>";
		
		echo "<div class='row' style='padding:0 15px 0 15px'><span id='btn-score-$questionid' class='modal-close waves-effect btn-flat white-text' style='background-color: "; echo getSiteColor(); echo "'>Score Question</span></div>";
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
					}
				});
				
				//Handle the response
				function formatResponseData(response)
				{	
		            lastSubmitted = JSON.parse(response.scores[0].response);
		            var score = response['scores'][0]['score'];
		            if(score==="1")
		            { 
			            score='Correct'; 
			            $('#pnl-score-<?php echo $questionid ?>').html('<div class="card white-text" style="background-color:#4CAF50; padding:20px;">'+score+'</div>');
			        }
			        if(score==="0")
		            {
				        score='Incorrect';
				        $('#pnl-score-<?php echo $questionid ?>').html('<div class="card white-text" style="background-color:#F44336; padding:20px;">'+score+'</div>');
				    }
				    if(score==="")
		            {
				        score='Rubric Graded';
				        $('#pnl-score-<?php echo $questionid ?>').html('<div class="card black-text" style="background-color:#FFEB3B; padding:20px;">'+score+'</div>');
				    }
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
		
<?php
		
	}
		
?>