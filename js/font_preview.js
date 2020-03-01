(function ($) {

    function change_weight(){

        var font_weight=$('select').val();
        var real_font_weight=font_weight/100*100;
        var italic=font_weight%100;
        var font_preview=$('.font_preview');
        if(italic)
        {
            $('.font_preview').css('font-style','italic');
        }
        else
        {
            $('.font_preview').css('font-style','normal');
        }
    
        $('.font_preview').css('font-weight',real_font_weight);
    }
    
    function change_size(){
        var font_size=Number($('#fontsize_range').val());
        $('.font_preview').css('font-size',font_size);
    }


    $(document).ready(function(){
        var select=$('select');
        select.niceSelect();

        //chooose different font-weight
        $('select').change(function(){

            change_weight();    

        })
        

        //choose different font-size
        $('#fontsize_range').change(function(){

            change_size();

        })

        $('#fontsize_range').mousemove(function(){

            change_size();

        })

        change_weight();
        change_size();

        var slider = document.getElementById("fontsize_range");
        var output = document.getElementById("font_size_show");
        output.innerHTML = slider.value+"pts";

        slider.oninput = function() {
            output.innerHTML = this.value+"pts";
        }


        $(".font_gallery .mb15 img").click(function(e){
            var font_family=$(this).parent().find(".font_family").html();
            $('.font_preview').css('font-family',font_family);
        })
    });
    
})(jQuery);

