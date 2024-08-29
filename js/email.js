
document.querySelector("#contactForm").addEventListener("submit", function(e){
    e.preventDefault();

  // Post form data via ajax request
  $.ajax({
    type:'POST',
    url:'form_submit.php',
    dataType: "json",
    data: $('#contactForm').serialize()+'&submit=1',
    beforeSend: function () {
    $(":input").prop("disabled", true);
    $(':button[type="submit"]').prop("disabled", true);
    $(':button[type="submit"]').text('Submitting...');
          },
    success:function(data){
    if(data.status == 1){
    $('#contactForm')[0].reset();
      $('.frm-status').html('<div class="alert alert-success">'+data.msg+'</div>');
    }else{
      $('.frm-status').html('<div class="alert alert-danger">' + data.msg + '</div>');
    }

    $(':button[type="submit"]').prop("disabled", false);
    $(':button[type="submit"]').text('Submit');
    $(":input").prop("disabled", false);

    // Reinitialize recaptcha widget
    grecaptcha.reset();
    }
    });
});

