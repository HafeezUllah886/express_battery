$(document).on('keydown', function(event) {
    if (event.key === 'F10') {
        event.preventDefault();
        $("#code").focus();
    }
    if (event.key === 'F6') {
        event.preventDefault();
        allProducts();
        var selectize = $(".selectize1")[0].selectize;
        if (selectize) {
            selectize.clear();
        }
    }
    if (event.key === 'F8') {
        event.preventDefault();
        var selectize = $(".selectize")[0].selectize;
        if (selectize) {
            selectize.focus();
        }
    }
    if (event.key === 'F9') {
        event.preventDefault();
        var selectize = $(".selectize1")[0].selectize;
        if (selectize) {
            selectize.focus();
        }
    }
    if (event.key === 'F2') {
        event.preventDefault();
        if ($("#detailsModal").is(":visible")) {
           $("#detailsForm").submit();
        } else {
            $("#productsForm").submit();
        }

    }
});

function fullscreen()
{
    var fullscreenBtn = $("#fullScreen");
    var element = document.documentElement;
    if (element.requestFullscreen) {
        if (document.fullscreenElement) {
            document.exitFullscreen();
            fullscreenBtn.text("Full Screen (F11)");
        } else {
            element.requestFullscreen();
            fullscreenBtn.text("Exit Full Screen (F11)");
        }
    } else if (element.mozRequestFullScreen) {
        if (document.mozFullScreenElement) {
            document.mozCancelFullScreen();
            fullscreenBtn.text("Full Screen (F11)");
        } else {
            element.mozRequestFullScreen();
            fullscreenBtn.text("Exit Full Screen (F11)");
        }
    } else if (element.webkitRequestFullscreen) {
        if (document.webkitFullscreenElement) {
            document.webkitExitFullscreen();
            fullscreenBtn.text("Full Screen (F11)");
        } else {
            element.webkitRequestFullscreen();
            fullscreenBtn.text("Exit Full Screen (F11)");
        }
    } else if (element.msRequestFullscreen) {
        if (document.msFullscreenElement) {
            document.msExitFullscreen();
            fullscreenBtn.text("Full Screen (F11)");
        } else {
            element.msRequestFullscreen();
            fullscreenBtn.text("Exit Full Screen (F11)");
        }
    }
}


let timeout;
$(".no_zero").on("input", function (){
    clearTimeout(timeout);  // Clear any previous timeout to avoid multiple triggers
var $this = $(this);

timeout = setTimeout(function() {
if ($this.val() === '') {
    $this.val(0);
    updateTotal();
}
}, 1000);  // 1000ms = 1 second
});

$("#code_form").on("submit", function(e)
    {
        e.preventDefault();
        var code = $("#code").val();
        $("#code").val('');
        $.ajax({
                url: "pos/searchByCode/" + code,
                method: "GET",
                success: function(response) {
                    if(response == "Not Found")
                    {
                        Toastify({
                        text: "Invalid Barcode",
                        className: "info",
                        close: true,
                        gravity: "top", // `top` or `bottom`
                        position: "center", // `left`, `center` or `right`
                        stopOnFocus: true, // Prevents dismissing of toast on hover
                        style: {
                            background: "linear-gradient(to right, #FF5733, #E70000)",
                        }
                        }).showToast();
                    }
                    else
                    {
                        getSingleProduct(response);
                    }
                }
            }
        );

    });



