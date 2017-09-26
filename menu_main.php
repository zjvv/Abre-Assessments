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
	require(dirname(__FILE__) . '/../../configuration.php'); 
	require_once(dirname(__FILE__) . '/../../core/abre_verification.php');
	require_once(dirname(__FILE__) . '/../../core/abre_functions.php');
	
?>

    <div class="col s12">
		<ul class="tabs_2" style='background-color: <?php echo sitesettings("sitecolor"); ?>'>
			<li class="tab col s3 tab_1 supportmenu pointer" data="#assessments"><a href="#assessments" class='mdl-color-text--white'>My Assessments</a></li>
			<li class="tab col s3 tab_3 supportmenu pointer" data="#assessments/public"><a href="#assessments/public" class='mdl-color-text--white'>Public</a></li>
		</ul>
	</div>
	
<script>
	
	$(function()
	{	
		$( ".supportmenu" ).unbind().click(function()
		{
			window.open($(this).attr("data"), '_self');
		});	
	});
	
</script>