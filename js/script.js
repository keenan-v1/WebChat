 $(function() {
    $.webChat = new Object();
    $.webChat.username = "No One";
    
    var dialog, form, name = $("#name");

    function sendUser() {
        var valid = true;
        var data = {
            "action": "set-username",
            "username": name.val()
            };
        data = $.param(data);        
        console.log("data: " + data);
          $.ajax({
            type: "POST",
            crossDomain: false,
            dataType: "json",
            url: "http://x.wurmly.com/test.php",
            data: data,
            success: function(data) {
                $.webChat.username = data['username'];
                console.log("Form submitted successfully.\nReturned json: " + data["json"]);
                dialog.dialog("close");             
            }
        });
        return valid;
    }
 
    dialog = $( "#dialog-form" ).dialog({
      autoOpen: false,
      height: 300,
      width: 350,
      modal: true,
      buttons: {
        "Create User": sendUser,
        Cancel: function() {
            dialog.dialog( "close" );
        }
      },
      close: function() {
        form[ 0 ].reset();
      },
      open: function() {
        $("#name").val($.webChat.username);
      }
    });
 
    form = dialog.find( "form" ).on( "submit", function( event ) {
      event.preventDefault();
      sendUser();
    });
    
    $(document).keypress(function(event){
        if(event.which == 117) {
            event.preventDefault();
            dialog.dialog("open");
        }
    });
  });