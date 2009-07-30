document.addEvent("domready", function(){
	
	var oSubjectSlide = new Fx.Slide($('subject_div'), {mode:'horizontal'});
	var oAppointmentsSlide = new Fx.Slide($('appointments_div'), {mode:'horizontal'}).hide();
	var oConfirmSlide = new Fx.Slide($('confirm_div'), {mode:'horizontal'}).hide();
	
	var oAppointments = {};
	var oSubjects = {};
	
	
	var oSubjectRequest = new Request.JSON({
		url:"subjects.php",
		secure:true,
		onSuccess:function(responseJSON, responseText){
			var responseJSON = JSON.decode(responseText);
			var eSelect = $('subject_select').empty();
			var eOption = null;
			eSelect.empty();
			responseJSON.each(function(item){
					eOption = new Element('option', {value:item.subjectid, text:item.subject});
					eSelect.adopt(eOption);
					oSubjects[item.subjectid] = item;
			});
		}
	}).get();
	
	
	var oAppointmentsRequest = new Request.JSON({
		url:"appointments.php",
		secure:true,
		onSuccess:function(responseJSON, responseText){
			var responseJSON = JSON.decode(responseText);
			var eSelect = $('appointments_select').empty();
			var eOption = null;
			var sAppointment = "";
			var oDate = new Date();
			eSelect.empty();
			responseJSON.each(function(item){
					//{"appointmentid":1004,"tutorid":1004, "tutorname":"Jake Harries", "timestamp":1250276400, "duration":5400}
					oDate.setTime(item.timestamp*1000);
					oEndDate = oDate.increment('second', item.duration);
					
					sAppointment = oDate.format("%B %d, %Y @ %H:%M")+' for '+ (item.duration/(60*60))+' hours';
					eOption = new Element('option', {value:item.appointmentid, text:sAppointment});
					eSelect.adopt(eOption);
					oAppointment = item;
					oAppointment.sAppointment = sAppointment;
					console.log('new oAppointment',oAppointment);
			
					oAppointments[item.appointmentid] = oAppointment;
					console.log('new oAppointments',oAppointments);
			});
			oAppointmentsSlide.slideIn();
		}
	});
	
	
	
	$('subject_select').addEvent('change', function(evt){
			oAppointmentsRequest.get({subject:$('subject_select').get('value')});
	});
	
	
	$('appointments_select').addEvent('change', function(evt){
			$("confirm_subject_text").set('html',  oSubjects[$('subject_select').get('value')].subject);
			var iAppointmentID = $('appointments_select').get('value');
			console.log('appointments_select',$('appointments_select'));
			
			console.log('iAppointmentID',iAppointmentID);
					console.log('oAppointments',oAppointments);
			
			oAppointment = oAppointments[iAppointmentID];
			console.log('oAppointment',oAppointment);
			
			$("confirm_tutorname_text").set('text', oAppointment.tutorname );
			if(oAppointment.image )
			{
				$("confirm_tutor_image").set('src', oAppointment.image ).show();
			}
			else
			{
				$("confirm_tutor_image").hide();
			}

			$("confirm_appointment_text").set('html', oAppointment.sAppointment );
			
			oConfirmSlide.slideIn();
	});


});