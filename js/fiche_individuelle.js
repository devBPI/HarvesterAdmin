for (let i = 1; i < number_of_alerts; i++)
    $(function() {
        $("#alertPopUp"+i).dialog({
            autoOpen: false,
            width: 400,
            show: {
                effect: "clip",
                duration: 200
            },
            hide: {
                effect: "clip",
                duration: 200
            }
        });
        $("#alertOpener"+i).on( "click", function() {
            $("#alertPopUp"+i).dialog( "open" );
        });
    });