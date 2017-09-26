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
		
			});
		
		</script>
		
<?php
	}
		
?>