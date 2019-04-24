$(document).ready(function() {
    $('#contact-form')
    	.bootstrapValidator({
	        feedbackIcons: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
	        },
	        fields: {
	            first_name: {
	                validators: {
	                    notEmpty: {
	                        message: 'Please supply your first name.'
	                    }
	                }
	            },
	            last_name: {
	                validators: {
	                    notEmpty: {
	                        message: 'Please supply your last name.'
	                    }
	                }
	            },
	            affiliation: {
	                validators: {
	                    notEmpty: {
	                        message: 'Please supply your affiliation.'
	                    }
	                }
	            },
	            institution: {
	                validators: {
	                    notEmpty: {
	                        message: 'Please supply your institution.'
	                    }
	                }
	            },
	            email: {
	                validators: {
	                    notEmpty: {
	                        message: 'Please supply your email address.'
	                    },
	                    emailAddress: {
	                        message: 'Please supply a valid email address.'
	                    }
	                }
	            },
	            phone: {
	                validators: {
	                    notEmpty: {
	                        message: 'Please supply your phone number.'
	                    }
	                }
	            },
	            nation: {
	                validators: {
	                    notEmpty: {
	                        message: 'Please supply your nation.'
	                    }
	                }
	            },
	            title: {
	                validators: {
	                    notEmpty: {
	                        message: 'Please supply your title.'
	                    }
	                }
	            },
	            topic: {
	                validators: {
	                    notEmpty: {
	                        message: 'Please select your topic.'
	                    }
	                }
	            },
	            file: {
	            	validators: {
	            		notEmpty: {
	            			message: 'You must select a valid file to upload.'
	            		},
	            		file: {
	            			extension: 'doc,docx',
	            			message: 'The file extension only accept: doc, docx.'
	            		}
	            	}
	            }
        	}
    	})
		.on('success.form.bv', function(e) {
			// $('#success_message').slideDown({ opacity: "show" }, "slow") // Do something ...
			// $('#contact_form').data('bootstrapValidator').resetForm();

	        // Prevent form submission
	        // e.preventDefault();

	        // Get the form instance
	        // var $form = $(e.target);

	        // Get the BootstrapValidator instance
	        // var bv = $form.data('bootstrapValidator');

	        // Use Ajax to submit form data
	        // $.post($form.attr('action'), $form.serialize(), function(result) {
	        //     console.log(result);
	        // }, 'json');
	    });

    $("#submit").click(function(e) {
    	e.preventDefault();

    	var formData = new FormData($('#contact-form')[0]);

	    $.ajax({
	    	url: './assets/php/formmail.php',
	    	data: formData,
	    	processData: false,
	    	contentType: false,
	    	type: 'POST',
	    	dataType: 'json',
	    	success: function(res) {
	    		console.log(res);
	    		$('#success_message').show();
	    		if (res.type === 'success') {
	    			console.log('success: ' + res.text);
	    		} else {
	    			console.log('error: ' + res.text);
	    		}
	    	},
	    	error: function(XMLHttpRequest, textStatus) {
	    		console.log(XMLHttpRequest);
	    		console.log(textStatus);
	    	}
	    })
    });

});