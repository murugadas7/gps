  <?php
    include_once("utils.php");

    $getTotalCounts = sqlFetchAll("SELECT count(id) as totals FROM `list_devices`"); 
    $total_records = $getTotalCounts[0][totals];
    $total_groups = ceil($total_records/$items_per_group);
  ?>
  <!DOCTYPE html>
  <html>
    <head>
      <!--Import Google Icon Font-->
      <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
      <!--Import materialize.css-->
      <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
      <style type="text/css">
 
        .brand-logo img{
          width:120px;
        }
        nav ul a{
          color: #213346;
          font-size: 14px;
          font-weight: 600;
          margin-top: 4px;
        }
 
        .sidenav-trigger {
          color: #213346;
        }

        i.left {
            float: left;
            margin-right: 5px;
            margin-top: -2px;
        }
        .bold{
          font-weight: 700;
        }
 
         .animation_image{
        display:none;font-size:26px; color:#213346; 
        }

        .ai{
          display:inherit !important;font-size:26px !important; margin:20px 20px;
        }
      </style>
      <!--Let browser know website is optimized for mobile-->
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    </head>

    <body>      
      <div class="navbar-fixed">
        <nav class="white" role="navigation">
          <div class="nav-wrapper container">
            <a id="logo-container" href="#" class="brand-logo"><img src="img/logo.png" ></a>
            <ul class="right hide-on-med-and-down">
              <li><a href="#">ABOUT US</a></li>
              <li><a href="#">CONTACT US</a></li>
              <li><a href="#">LOGIN</a></li>
              <li><a href="#">FREE DEMO</a></li>
            </ul>


            <a href="#" data-target="nav-mobile" class="sidenav-trigger"><i class="material-icons">menu</i></a>
          </div>

        </nav>

      </div>
        <ul id="nav-mobile" class="sidenav">
          <li><a href="#">ABOUT US</a></li>
          <li><a href="#">CONTACT US</a></li>
          <li><a href="#">LOGIN</a></li>
          <li><a href="#">FREE DEMO</a></li>
        </ul>
         
          <div class="container">
            <h4 class="header center orange-text">List Telematics Devices </h4>
          <table class="striped">
              <thead>
                <tr>
                    <th>Device ID</th>
                    <th>Device Name</th>
                    <th>Last Reported Time</th>
                    <th>Status</th>
                </tr>
              </thead>

              <tbody id="results">
               
              </tbody>
            </table>
 
            <div class="animation_image"  align="center">Loading</div>

          </div>
          <br><br>
        <footer class="page-footer red accent-2">
          <div class="footer-copyright">
            <div class="container">
            Designed by <a class="white-text bold" href="https://fleetsu.com/">Fleetsu</a>
            </div>
          </div>
        </footer>

      <!--JavaScript at end of body for optimized loading-->
      <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
      <script type="text/javascript" src="js/materialize.min.js"></script>
      <script>
        (function($){
          $(function(){
            $('.sidenav').sidenav();
          }); // end of document ready
        })(jQuery); // end of jQuery name space

        var route = "<?php e(G_LINK); ?>/"; 
        var pageNo = 0; 
        var loading  = false; 
        var totalnum = <?php e($total_groups); ?>; 

        function formatDate(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear(),
                hours = d.getHours(),
                minutes = d.getMinutes();

            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;


            var month = new Array();
            month[0] = "Jan";
            month[1] = "Feb";
            month[2] = "Mar";
            month[3] = "Apr";
            month[4] = "May";
            month[5] = "Jun";
            month[6] = "Jul";
            month[7] = "Aug";
            month[8] = "Sep";
            month[9] = "Oct";
            month[10] = "Nov";
            month[11] = "Dec";
            var month = month[d.getMonth()];

            var getDateTime = ''+ day + ' '+ month + ' '+ year +', '+ hours +':'+  minutes;

            return getDateTime;
        }

 
function loadProduct(data){ 
   
  if(data != ''){            
    $.each(data, function(key, value){
      var device_id = value.device_id;
      var device_label = value.device_label;
      var report_time = value.last_reported_time;
      var currentDate = new Date();
      var lastReportedDate = new Date(''+ report_time +' UTC');
      var one_day = new Date(lastReportedDate); // your date object
      one_day.setHours(one_day.getHours() + 24);
      

      if(currentDate < one_day){
        status = '<td class="green-text bold"><i class="material-icons left">&#xE1B3;</i> ON</td>';
      }else{
        status = '<td class="materialize-red-text bold"><i class="material-icons left">&#xE1B5;</i> OFFLINE</td>';
      }
 
      $('#results').append('<tr><td>'+ device_id +'</td><td>'+ device_label +'</td><td>'+ formatDate(lastReportedDate) +'</td>'+ status +'</tr>');
    }); 
  }else{
     
    $('.animation_image').text('No Record Found');
    $('.animation_image').addClass("ai");
  }
}

 
 
function LoadFilterContent(info){ 
        //var orderBy = $("#orderBy option:selected").val();
    
        $.ajax({
           type: "POST",
           data:{ 'page_no' : pageNo },
           url: route + 'api.php',
           beforeSend: function(x) {              
            $('.animation_image').show(); 
            if(x && x.overrideMimeType) {
              x.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         dataType: "json",
         success: function(data){
           $('.animation_image').hide();           
            loadProduct(data);             
           }
        });
}


  LoadFilterContent();
  $(window).scroll(function() { 
    
    if($(window).scrollTop() + $(window).height() == $(document).height())  
    {
      
      if(pageNo < totalnum  && loading==false) 
      {        
          pageNo++; 
          LoadFilterContent();         
          loading = false;
        
      }
    }

  });
      </script>
    </body>
  </html>