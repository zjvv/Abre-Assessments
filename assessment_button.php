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
	require_once('permissions.php');
	
	if($pagerestrictions=="")
	{
	
	?>
			<div class='fixed-action-btn buttonpin'>
				<a class='modal-createassessment btn-floating btn-large waves-effect waves-light' style='background-color: <?php echo getSiteColor(); ?>' id="createassessmenttooltip" data-grade='blank' data-subject='blank' data-position='left' data-title='' data-grade='blank' data-subject='blank' href='#createassessment'><i class='large material-icons'>add</i></a>
				<div class="mdl-tooltip mdl-tooltip--left" for="createassessmenttooltip">Create Assessment</div>
			</div>
	<?php
	}
	?>

<script>
	
	$(function()
	{
		
    	$('.modal-createassessment').leanModal({
	    	in_duration: 0,
			out_duration: 0,
	    	ready: function() {
		    	$("#assessment_title").focus();
		    }
    	});
    	
  	});
  	
</script>