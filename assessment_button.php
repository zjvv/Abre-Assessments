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
	require_once(dirname(__FILE__) . '/../../core/abre_functions.php');
	require_once('permissions.php');
	
	if($pagerestrictions=="")
	{
	
		if(superadmin())
		{
	?>
			<div class="fixed-action-btn">
		    <a class="btn-floating btn-large" style='background-color: <?php echo sitesettings("sitecolor"); ?>'>
		      <i class="material-icons">add</i>
		    </a>
		    <ul>
		      <li><a class="modal-createassessment btn-floating btn-large" style='background-color: <?php echo sitesettings("sitecolor"); ?>' id="createassessmenttooltip" href='#createassessment'><i class="material-icons">edit</i></a></li>
		      <div class="mdl-tooltip mdl-tooltip--left" for="createassessmenttooltip">Create Assessment</div>
		      <li><a class="btn-floating btn-large" style='background-color: <?php echo sitesettings("sitecolor"); ?>' id="assessmentsettingstooltip" href='#assessments/settings'><i class="material-icons">settings</i></a></li>
		      <div class="mdl-tooltip mdl-tooltip--left" for="assessmentsettingstooltip">Settings</div>
		    </ul>
		  	</div>
	<?php
		}
		else
		{
	?>
			<div class='fixed-action-btn buttonpin'>
				<a class='modal-createassessment btn-floating btn-large waves-effect waves-light' style='background-color: <?php echo sitesettings("sitecolor"); ?>' id="createassessmenttooltip" data-grade='blank' data-subject='blank' data-position='left' data-title='' data-grade='blank' data-subject='blank' href='#createassessment'><i class='large material-icons'>edit</i></a>
				<div class="mdl-tooltip mdl-tooltip--left" for="createassessmenttooltip">Create Assessment</div>
			</div>
	<?php
		}
		
	}
	?>

<script>
	
	$(function()
	{
		
    	$('.modal-createassessment').leanModal({
	    	in_duration: 0,
			out_duration: 0,
	    	ready: function() { $("#assessment_title").focus(); }
    	});
    	
  	});
  	
</script>