
jQuery(function () {
    // Initialize datepicker.
    jQuery("#datepicker").datepicker({
        dateFormat: 'mm/dd/yy',
        startDate: '-3d'
    });

    // Initialize form validation on the registration form.
    // It has the name attribute "registration"
//    jQuery("form[name='eventform']").validate({
//        // Specify validation rules
//        rules: {
//            // The key name on the left side is the name attribute
//            // of an input field. Validation rules are defined
//            // on the right side
//            event_name: "required",
//            event_desc: "required",
//            event_date: "required",
//            event_category: "required",
//            event_image: "required",
//        },
//        // Specify validation error messages
//        messages: {
//            event_name: "Please enter your event name",
//            event_desc: "Please enter your event description",
//            event_date: "Please enter your event date",
//            event_category: "Please enter your event city",
//            event_image: "Please enter your event photo",
//        },
//        // Make sure the form is submitted to the destination defined
//        // in the "action" attribute of the form when valid
//        submitHandler: function (form) {
//            form.submit();
//        }
//    });
});
