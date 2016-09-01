<?php

	###################################################
	### Name: telephonyinbound.php 					###
	### Functions: Manage Inbound, IVR & DID  		###
	### Copyright: GOAutoDial Ltd. (c) 2011-2016	###
	### Version: 4.0 								###
	### Written by: Alexander Jim H. Abenoja		###
	### License: AGPLv2								###
	###################################################

	require_once('./php/UIHandler.php');
	require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');

	$ui = \creamy\UIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Audio Files</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        
        <?php print $ui->standardizedThemeCSS(); ?>
    	
    	<!-- Wizard Form style -->
        <link href="css/style.css" rel="stylesheet" type="text/css" />
        
    	<!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
       
        <?php print $ui->creamyThemeCSS(); ?>
		
		<!-- Data Tables -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
        
        <script type="text/javascript">
			$(window).ready(function() {
				$(".preloader").fadeOut("slow");
			})
		</script>

    </head>
    
    <?php print $ui->creamyBody(); ?>

        <div class="wrapper">	
        <!-- header logo: style can be found in header.less -->
		<?php print $ui->creamyHeader($user); ?>
            <!-- Left side column. contains the logo and sidebar -->
			<?php print $ui->getSidebar($user->getUserId(), $user->getUserName(), $user->getUserRole(), $user->getUserAvatar()); ?>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        <?php $lh->translateText("telephony"); ?>
                        <small><?php $lh->translateText("audiofiles_management"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li><?php $lh->translateText("telephony"); ?></li>
						<li class="active"><?php $lh->translateText("audiofiles"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>

			<div class="panel panel-default">
				<div class="panel-body">
					<legend>Audio Files </legend>

		            <div role="tabpanel">
						
						<ul role="tablist" class="nav nav-tabs nav-justified">

						<!-- Voicefiles panel tab -->
							 <li role="presentation" class="active">
								<a href="#voicefiles_tab" aria-controls="voicefiles_tab" role="tab" data-toggle="tab" class="bb0">
								    Voice Files </a>
							 </li>
						 <!-- MOH panel tabs-->
							 <li role="presentation" >
								<a href="#moh_tab" aria-controls="moh_tab" role="tab" data-toggle="tab" class="bb0">
								    Music On-Hold</a>
							 </li>
						
						  </ul>
						  
						<!-- Tab panes-->
						<div class="tab-content bg-white">

							<!--==== MOH ====-->
							<div id="moh_tab" role="tabpanel" class="tab-pane">
								<?php print $ui->getListAllMusicOnHold(); ?>
							</div>

							<!--==== Voicefiles ====-->
							<div id="voicefiles_tab" class="tab-pane Active">
								<?php print $ui->getListAllVoiceFiles(); ?>
							</div>

						</div><!-- END tab content-->

							<!-- /fila con acciones, formularios y demás -->
							<?php
								} else {
									print $ui->calloutErrorMessage($lh->translationFor("you_dont_have_permission"));
								}
							?>
							
						<div class="bottom-menu skin-blue">
							<div class="action-button-circle" data-toggle="modal">
								<?php print $ui->getCircleButton("inbound", "plus"); ?>
							</div>
							<div class="fab-div-area" id="fab-div-area">
								<ul class="fab-ul" style="height: 170px;">
									<li class="li-style"><a class="fa fa-volume-up fab-div-item" data-toggle="modal" data-target="#form-voicefiles-modal" title="Add a Voice File"></a></li><br/>
									<li class="li-style"><a class="fa fa-music fab-div-item" data-toggle="modal" data-target="#moh-wizard" title="Add a Music On-hold"></a></li><br/>
								</ul>
							</div>
						</div>
					</div>
				</div><!-- /. body -->
			</div><!-- /. panel -->
        </section><!-- /.content -->
    </aside><!-- /.right-side -->
</div><!-- ./wrapper -->

<?php
 /*
  * APIs needed for form
  */
   $user_groups = $ui->API_goGetUserGroupsList();
?>
<!-- MOH MODALS -->
	<!-- Modal -->
	<div id="view-moh-modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><b>Music On Hold Details</b></h4>
	      </div>
	      <div class="modal-body">
		<div class="form-horizontal">
			
			<div class="form-group">
				<label class="control-label col-lg-4">Music on Hold Name:</label>
				<div class="col-lg-7">
					<input type="text" class="form-control moh_name">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-4">Status:</label>
				<div class="col-lg-5">
					<select class="form-control moh_status">
						<option value="Y">Active</option>
						<option value="N">Inactive</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-4">User Group:</label>
				<div class="col-lg-7">
					<select class="form-control moh_user_group">
						<?php
                            for($i=0;$i<count($user_groups->user_group);$i++){
                        ?>
                            <option value="<?php echo $user_groups->user_group[$i];?>">  <?php echo $user_groups->user_group[$i].' - '.$user_groups->group_name[$i];?>  </option>
                        <?php
                            }
                        ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-4">Random Order:</label>
				<div class="col-lg-5">
					<select class="form-control moh_rand_order">
						<option value="Y">Yes</option>
						<option value="N">No</option>
					</select>
				</div>
			</div>
		</div>
	      </div>
          <div class="message_box"></div>
	      <div class="modal-footer">
	           <button type="button" class="btn btn-primary btn-update-moh-info" data-id=""><span id="update_button"><i class="fa fa-check"></i> Update</span></button>
	           <!--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>-->
	      </div>
	    </div>
	    <!-- End of modal content -->
	  </div>
	</div>
	<!-- End of modal -->

	<!-- ADD USER GROUP MODAL -->
    <div class="modal fade" id="moh-wizard" tabindex="-1" aria-labelledby="moh-wizard" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">

            <!-- Header -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close_ingroup"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title animated bounceInRight"><b>Music On Hold Wizard » Add New Music On Hold</b></h4>
                </div>
                <div class="modal-body wizard-content">
                
                <form action="" method="POST" id="create_moh" name="create_moh" role="form">
                    <div class="row">
                    	<h4>Music On-Hold<br/>
                    	<small>Music On-Hold details and assign to a user group.</small>
                    	</h4>
                    	<fieldset>
	                        <div class="form-group">
	                            <label class="col-sm-4 control-label" for="moh_id">Music On Hold ID:</label>
	                            <div class="col-sm-8 mb">
	                                <input type="text" name="moh_id" id="moh_id" class="form-control" placeholder="Music on Hold ID (Mandatory)" required />
	                            </div>
	                        </div>
	                        <div class="form-group">        
	                            <label class="col-sm-4 control-label" for="moh_name">Music On Hold Name: </label>
	                            <div class="col-sm-8 mb">
	                                <input type="text" name="moh_name" id="moh_name" class="form-control" placeholder="Music On Hold Name (Mandatory)" required />
	                            </div>
	                        </div>
	                        <div class="form-group">
	                            <label class="col-sm-4 control-label" for="active">Status: </label>
	                            <div class="col-sm-8 mb">
	                                <select name="active" id="active" class="form-control">
	                                    <option value="N" selected>INACTIVE</option>
	                                    <option value="Y">ACTIVE</option>
	                                </select>
	                            </div>
	                        </div>
	                        <div class="form-group">
	                            <label class="col-sm-4 control-label" for="user_group">User Group: </label>
	                            <div class="col-sm-8 mb">
	                                <select id="user_group" class="form-control" name="user_group">
	                                	<!--<option value="---ALL---">  ALL USER GROUPS  </option>-->
	                                    <?php
	                                        for($i=0;$i<count($user_groups->user_group);$i++){
	                                    ?>
	                                        <option value="<?php echo $user_groups->user_group[$i];?>">  <?php echo $user_groups->user_group[$i].' - '.$user_groups->group_name[$i];?>  </option>
	                                    <?php
	                                        }
	                                    ?>
	                                </select>
	                            </div>
	                        </div>
	                        <div class="form-group">
	                            <label class="col-sm-4 control-label" for="random">Random Order: </label>
	                            <div class="col-sm-8 mb">
	                                <select name="random" id="random" class="form-control">
	                                    <option value="N" selected>NO</option>
	                                    <option value="Y">YES</option>
	                                </select>
	                            </div>
	                        </div>
                        </fieldset>
                    </div><!-- end of step -->
                
                </form>

                </div> <!-- end of modal body -->
            </div>
        </div>
    </div><!-- end of modal -->
<!-- end of MOH Modals -->

<!-- VOICE FILES MODALS -->
	<!-- Playback Modal -->
	<div id="voice-playback-modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><b>Voice Files Playback</b></h4>
	      </div>
	      <div class="modal-body">
		<div class="voice-player"></div>
	      	<!-- <audio controls>
			<source src="http://www.w3schools.com/html/horse.ogg" type="audio/ogg" />
			<source src="http://www.w3schools.com/html/horse.mp3" type="audio/mpeg" />
			<a href="http://www.w3schools.com/html/horse.mp3">horse</a>
		</audio> -->
	      </div>
	      <div class="modal-footer">
		<a href="" class="btn btn-primary download-audio-file" download>Download File</a>
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div>
	    <!-- End of modal content -->
	  </div>
	</div>
	<!-- End of modal -->

	<!-- Upload Voice Files Modal -->
	<div id="form-voicefiles-modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
	      	<div class="modal-header">
	        	<button type="button" class="close" data-dismiss="modal">&times;</button>
	       		<h4 class="modal-title animated bounceInRight"><b>Upload Voice Files</b></h4>
	      	</div>
		    <div class="modal-body clearfix">
		        <form action="./php/AddVoiceFiles.php" method="POST" id="voicefile_form" enctype="multipart/form-data">
		  	      	<div class="row">
		  	      		<h4>
		  	      			Upload a Voice File<br/>
		  	      			<small>Browse for the file and click on Submit.</small>
		  	      		</h4>
		  	      		<fieldset>
		    				<div class="col-lg-12">
		    					<div class="form-group mt">
		    						<div class="input-group">
		    					      	<input type="text" class="form-control voice_file_holder" placeholder="Choose a file" required>
		    					      	<span class="input-group-btn">
		    					        	<button class="btn btn-default btn-browse-file" type="button">Browse...</button>
		    					     	</span>
		    					    </div><!-- /input-group -->
		    					    <input type="file" name="voice_file" class="hide" id="voice_file" accept="audio/*">
		    					</div>
		    				</div>
		    				<div class="form-group">
		    					<div class="upload-loader" style="display:none;">
					    			<center>
					    				<div class="fl spinner2" style="position: absolute;">
					    					<div class="spinner-container container1">
					    						<div class="circle1"></div>
					    						<div class="circle2"></div>
					    						<div class="circle3"></div>
					    						<div class="circle4"></div>
					    					</div>
					    					<div class="spinner-container container2">
					    						<div class="circle1"></div>
					    						<div class="circle2"></div>
					    						<div class="circle3"></div>
					    						<div class="circle4"></div>
					    					</div>
					    					<div class="spinner-container container3">
					    						<div class="circle1"></div>
					    						<div class="circle2"></div>
					    						<div class="circle3"></div>
					    						<div class="circle4"></div>
					    					</div>
					    					<h4 class="upload-text"><b>Uploading...</b></h4>
					    				</div>
					    			</center>
					    		</div>
		    				</div>
		    			</fieldset>
		    		</div>
		        </form>
		    </div>
	    </div>
	    <!-- End of modal content -->
	  </div>
	</div>
	<!-- End of modal -->
<!-- End of VOICE FILE Modals -->

		<?php print $ui->standardizedThemeJS(); ?>
        <!-- JQUERY STEPS-->
  		<script src="theme_dashboard/js/jquery.steps/build/jquery.steps.js"></script>

 <script type="text/javascript">
	$(document).ready(function() {

		/*******************
		** INITIALIZATIONS
		*******************/

			// loads the fixed action button
				$(".bottom-menu").on('mouseenter mouseleave', function () {
				  $(this).find(".fab-div-area").stop().slideToggle({ height: 'toggle', opacity: 'toggle' }, 'slow');
				});

			//loads datatable functions
				$('#music-on-hold_table').dataTable();
				$('#voicefiles').dataTable();
		
		/*******************
		** MOH EVENTS
		*******************/                
				
			/*********
			** INIT WIZARD
			*********/
			
				var moh_form = $("#create_moh"); // init form wizard 

			    moh_form.validate({
			        errorPlacement: function errorPlacement(error, element) { element.after(error); }
			    });
			    moh_form.children("div").steps({
			        headerTag: "h4",
			        bodyTag: "fieldset",
			        transitionEffect: "slideLeft",
			        onStepChanging: function (event, currentIndex, newIndex)
			        {
			        	// Allways allow step back to the previous step even if the current step is not valid!
				        if (currentIndex > newIndex) {
				            return true;
				        }

						// Clean up if user went backward before
					    if (currentIndex < newIndex)
					    {
					        // To remove error styles
					        $(".body:eq(" + newIndex + ") label.error", moh_form).remove();
					        $(".body:eq(" + newIndex + ") .error", moh_form).removeClass("error");
					    }

			            moh_form.validate().settings.ignore = ":disabled,:hidden";
			            return moh_form.valid();
			        },
			        onFinishing: function (event, currentIndex)
			        {
			            moh_form.validate().settings.ignore = ":disabled";
			            return moh_form.valid();
			        },
			        onFinished: function (event, currentIndex)
			        {

			        	$('#finish').text("Loading...");
			        	$('#finish').attr("disabled", true);

			        	/*********
						** ADD EVENT 
						*********/
				            // Submit form via ajax
					            $.ajax({
		                            url: "./php/AddMOH.php",
		                            type: 'POST',
		                            data: $("#create_moh").serialize(),
		                            success: function(data) {
		                              // console.log(data);
		                                  if(data == 1){
		                                        swal("Success!", "Music On Hold Successfully Created!", "success");
		                                        window.setTimeout(function(){location.reload()},3000)
		                                        $('#submit_moh').val("Submit");
		                                        $('#submit_moh').prop("disabled", false);
		                                  }
		                                  else{
		                                      sweetAlert("Oops...", "Something went wrong!"+data, "error");
		                                      $('#submit_moh').val("Submit");
		                                      $('#submit_moh').prop("disabled", false);
		                                  }
		                            }
		                        });
			        }
			    }); // end of wizard
			
			//------------------------

			/*********
			** EDIT MOH
			*********/

				$(document).on('click','.edit-moh',function() {

					var moh_id = $(this).attr('data-id');

					$.ajax({
						url: "./php/ViewMOH.php",
						type: 'POST',
						data: { 
						      moh_id : moh_id,
						},
						dataType: 'json',
						success: function(data) {
						      $('.btn-update-moh-info').attr('data-id', data.moh_id);
						      $('.moh_name').val(data.moh_name);
						      $('.moh_status option[value="' + data.active + '"]').attr('selected','selected');
						      $('.moh_user_group option[value="' + data.user_group + '"]').attr('selected','selected');
						      $('.moh_rand_order option[value="' + data.random + '"]').attr('selected','selected');
						  
                              $('#view-moh-modal').modal('show');
						}
					});
				});
				
				$('.btn-update-moh-info').click(function(){
                    $('#update_button').html("<i class='fa fa-edit'></i> Updating...");
                    $('.btn-update-moh-info').attr("disabled", true);

					$.ajax({
						url: "./php/UpdateMOH.php",
						type: 'POST',
						data: { 
						      moh_id : $(this).attr('data-id'),
						      moh_name : $('.moh_name').val(),
						      user_group : $('.mog_user_group').val(),
						      active : $('.moh_status').val(),
						      random : $('.moh_rand_order').val(),
						},
						dataType: 'json',
						success: function(data) {
						      if (data.result == "success") {
							    swal("Success!", "Music On Hold Successfully Updated!", "success");
                                window.setTimeout(function(){location.reload()},2000)   
                                
                                $('#update_button').html("<i class='fa fa-check'></i> Update");
                                $('.btn-update-moh-info').attr("disabled", false);
						      } else {
    							sweetAlert("Oops...", "Something went wrong! "+data, "error");
                                
                                $('#update_button').html("<i class='fa fa-check'></i> Update");
                                $('.btn-update-moh-info').attr("disabled", false);
						      }
						      
						      
						}
					});
				});

			/*********
			** DELETE MOH
			*********/
				// delete click
					$(document).on('click','.delete-moh',function() {
					 	var id = $(this).attr('data-id');
	                    swal({   
	                        title: "Are you sure?",   
	                        text: "This action cannot be undone.",   
	                        type: "warning",   
	                        showCancelButton: true,   
	                        confirmButtonColor: "#DD6B55",   
	                        confirmButtonText: "Yes, delete this moh!",   
	                        cancelButtonText: "No, cancel please!",   
	                        closeOnConfirm: false,   
	                        closeOnCancel: false 
	                        }, 
	                        function(isConfirm){   
	                            if (isConfirm) { 
	                                $.ajax({
	                                    url: "./php/DeleteMOH.php",
	                                    type: 'POST',
	                                    data: { 
	                                        moh_id:id,
	                                    },
	                                    success: function(data) {
	                                    console.log(data);
	                                        if(data == 1){
	                                           swal("Success!", "Music On Hold Successfully Deleted!", "success");
	                                           window.setTimeout(function(){location.reload()},1000)
	                                        }else{
	                                            sweetAlert("Oops...", "Something went wrong! "+data, "error");
	                                        }
	                                    }
	                                });
	                            } else {     
	                                    swal("Cancelled", "No action has been done :)", "error");   
	                            } 
	                        }
	                    );
					});
		
		//-------------------- end of main moh events

		/*******************
		** VOICEFILES EVENTS
		*******************/

			/********
			** INIT WIZARD
			*******/
				var voicefile_form = $("#voicefile_form"); // init form wizard 

			    voicefile_form.validate({
			        errorPlacement: function errorPlacement(error, element) { element.after(error); }
			    });
			    voicefile_form.children("div").steps({
			        headerTag: "h4",
			        bodyTag: "fieldset",
			        transitionEffect: "slideLeft",
			        onStepChanging: function (event, currentIndex, newIndex)
			        {
			        	// Allways allow step back to the previous step even if the current step is not valid!
				        if (currentIndex > newIndex) {
				            return true;
				        }

						// Clean up if user went backward before
					    if (currentIndex < newIndex)
					    {
					        // To remove error styles
					        $(".body:eq(" + newIndex + ") label.error", moh_form).remove();
					        $(".body:eq(" + newIndex + ") .error", moh_form).removeClass("error");
					    }

			            voicefile_form.validate().settings.ignore = ":disabled,:hidden";
			            return voicefile_form.valid();
			        },
			        onFinishing: function (event, currentIndex)
			        {
			            voicefile_form.validate().settings.ignore = ":disabled";
			            return voicefile_form.valid();
			        },
			        onFinished: function (event, currentIndex)
			        {

			        	$('#finish').text("Loading...");
			        	$('#finish').attr("disabled", true);
			        	$('.upload-loader').show();

			        	/*********
						** ADD EVENT 
						*********/
				            // submit form
				            	voicefile_form.submit();
			        }
			    }); // end of wizard

			// upload result
				<?php 
					if($_GET['upload_result'] == "success") {
				?>
						swal(
							{
								title: "Success",
								text: "Voice File Successfully Uploaded!",
								type: "success"
							},
							function(){
								window.location.href = 'audiofiles.php';
							}
						);
				<?php
					}elseif($_GET['upload_result'] == "error"){
				?>
						swal(
							{
								title: "Oops...",
								text: "Something went wrong.",
								type: "error"
							},
							function(){
								window.location.href = 'audiofiles.php';
							}
						);
				<?php
					} 
				?>
			
			// On play
				$('.play_voice_file').click(function(){
					var audioFile = $(this).attr('data-location');

					var sourceFile = '<audio class="audio_file" controls>';
					    sourceFile += '<source src="'+ audioFile +'" type="audio/mpeg" download="true"/>';
					    sourceFile += '</audio>';

					$('.download-audio-file').attr('href', audioFile);
					$('.voice-player').html(sourceFile);
					$('#voice-playback-modal').modal('show');

					var aud = $('.audio_file').get(0);
					aud.play();
				});

			// pause
				$('#voice-playback-modal').on('hidden.bs.modal', function () {
					var aud = $('.audio_file').get(0);
					aud.pause();
				});

			// browse
				$('.btn-browse-file').click(function(){
					$('#voice_file').click();
				});

			//voice_file
				$('#voice_file').change(function(){
					var myFile = $(this).prop('files');
					var Filename = myFile[0].name;
			        var filesize = myFile[0].size  / 1024;
			        filesize = (Math.round(filesize * 100) / 100)

			        if(filesize > 16000){
			            alert("The voice file you are trying to upload exceeds the required file size. Maximum file size is up to 16MB only.");
			            $('#voice_file').val('');
			            $('.voice_file_holder').val();
			        }else{
			            $('.voice_file_holder').val(Filename);
			        }
				});

			//voice_file_holder
		        $('.voice_file_holder').change(function(){
		          var holderVal = $(this).val();
		          var file = $('#voice_file').val();

		          if(holderVal != file){
		            $('#voice_file').val('');
		          }
		        });

		    // clear form
		        $('#form-voicefiles-modal').on('hidden.bs.modal', function () {
		            $('#voice_file').val('');
		            $('.voice_file_holder').val();
		        });
			
		//-------------------- end of main voice files events

		/*******************
		** FILTERS
		*******************/

			// disable special characters on Script ID
				$('#moh_id').bind('keypress', function (event) {
				    var regex = new RegExp("^[a-zA-Z0-9]+$");
				    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
				    if (!regex.test(key)) {
				       event.preventDefault();
				       return false;
				    }
				});

			// disable special characters on MOH Name
				$('#moh_name').bind('keypress', function (event) {
				    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
				    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
				    if (!regex.test(key)) {
				       event.preventDefault();
				       return false;
				    }
				});
	});
</script>

		<?php print $ui->creamyFooter(); ?>
    </body>
</html>