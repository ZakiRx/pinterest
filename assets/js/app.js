/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../scss/app.scss';
import '../scss/dashboard.scss';


// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.

import $ from 'jquery'
import 'bootstrap'



$(".custom-file-input").on("change",function (e) {
    var inputFile = e.currentTarget;
    $(inputFile).parent().find(".custom-file-label").text(inputFile.files[0].name)
});

const axios = require('axios');
$('#addComment').bind('submit',function(e) {
    e.preventDefault();
    let comment = $(".input-comment").val();
    const url=$("#url").val();
    axios.post(url,{'comment' : comment}).then(function (response){
        if(response.status==="200"){
            $(".list-comment").append('<div class="comment"><div class="d-flex"><img src="/images/pins/5ad68890d7289-thumb900-5f87834d446a6911649320.jpg" class="rounded-circle mr-1" alt="" width="42px" height="42px"> <p class="content" >'+comment+'</p>  </div>   <small class="date-comment">'+response.data.createdAt +'</small> </div>');
            $(".input-comment").val("")
        }

    });
});
