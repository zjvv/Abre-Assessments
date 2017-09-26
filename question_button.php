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
	require_once(dirname(__FILE__) . '/../../core/abre_functions.php');

?>

	<div class='fixed-action-btn buttonpin'>
		<?php
		echo "<a class='modal-addquestion btn-floating btn-large waves-effect waves-light' style='background-color:".sitesettings("sitecolor")."' id='addquestion' data-assessmentid='$Assessment_ID' href='#assessmentquestion'><i class='large material-icons'>add</i></a>";
		echo "<div class='mdl-tooltip mdl-tooltip--left' for='addquestion'>Add Question</div>";
		?>
	</div>


<script>
	
	$(function()
	{
		
    	$('.modal-addquestion').leanModal({
	    	in_duration: 0,
			out_duration: 0,
	    	ready: function() { $('.modal-content').scrollTop(0); },
	    	complete: function()
	    	{ 
		    	/*
		    	$('select').prop('selectedIndex', 0);
				$('select').material_select(); 
		    	$('#topicFiles').hide();
		    	$("#content_holder").load( "modules/<?php echo basename(__DIR__); ?>/assessment.php?id="+<?php echo $Assessment_ID; ?>, function(){
					mdlregister();
				});
				*/
				location.reload();
		    }
	   	});
    	
  	});
  	
</script>