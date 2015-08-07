<!DOCTYPE html>
<html lang="en">
<head>
  <!--
  Code By Kris Occhipinti
  August 4th, 2015
  Licensed under GPLv3
  http://www.gnu.org/licenses/gpl-3.0.txt  
  -->
  <title>Callback Calendar Mobile</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

  <!--Favicon-->
  <link rel="apple-touch-icon" sizes="57x57" href="icons/apple-touch-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="icons/apple-touch-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="icons/apple-touch-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="icons/apple-touch-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="icons/apple-touch-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="icons/apple-touch-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="icons/apple-touch-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="icons/apple-touch-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="icons/apple-touch-icon-180x180.png">
  <link rel="icon" type="image/png" href="icons/favicon-32x32.png" sizes="32x32">
  <link rel="icon" type="image/png" href="icons/android-chrome-192x192.png" sizes="192x192">
  <link rel="icon" type="image/png" href="icons/favicon-96x96.png" sizes="96x96">
  <link rel="icon" type="image/png" href="icons/favicon-16x16.png" sizes="16x16">
  <link rel="manifest" href="icons/manifest.json">
  <link rel="shortcut icon" href="icons/favicon.ico">
  <meta name="apple-mobile-web-app-title" content="Callback Mobile">
  <meta name="application-name" content="Callback Mobile">
  <meta name="msapplication-TileColor" content="#b91d47">
  <meta name="msapplication-TileImage" content="icons/mstile-144x144.png">
  <meta name="msapplication-config" content="icons/browserconfig.xml">
  <meta name="theme-color" content="#ffffff">


  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

<!--  <script src="https://raw.githubusercontent.com/metalx1000/Callback-Staffing-Improver/master/uploaders/mobile.js"></script>-->
<!--  <script src="https://rawgit.com/metalx1000/Callback-Staffing-Improver/master/uploaders/mobile.js"></script> -->
<!--  <script src="https://cdn.rawgit.com/metalx1000/Callback-Staffing-Improver/master/uploaders/mobile.js"></script>-->
  <script>
    var d,startDate,endDate;
    var days = [];
    var names = [];

    $(document).ready(function(){
      defaultDates();
      update();

      $("#trucks").val(localStorage.calltruck);
      $("#names").change(function(){
        filter();
      });

      $("#trucks").change(function(){
        filter();
      });

      $(".date").change(function(){
        update();
      });
    });


    function filter(){
      var n=$("#names").val();//.toUpperCase();
      var t=$("#trucks").val();
      localStorage.callname = n;
      localStorage.calltruck = t;
      
      $(".item").hide();
     
      $(".item").each(function(){ 
        if($(this).is(":contains('"+t+"')") && $(this).is(":contains('"+n+"')")){
          $(this).show();
        }
      });
    }

    function unique(arr) {
        var hash = {}, result = [];
        for ( var i = 0, l = arr.length; i < l; ++i ) {
            if ( !hash.hasOwnProperty(arr[i]) ) { //it works with objects! in FF, at least
                hash[ arr[i] ] = true;
                result.push(arr[i]);
            }
        }
        return result;
    }

    function colors(){

        $(".FF").css("color","white");
        $(".FF").css("background-color","red");

        $(".LT").css("color","white");
        $(".LT").css("background-color","green");

        $(".ENG").css("color","white");
        $(".ENG").css("background-color","blue");

        $(".notes").css("background-color","yellow");
    }

    function nameList(){
      $("#names").html("<option></option>");
      names=unique(names);
      names.sort();
      names=unique(names);
      names.sort();
      names.forEach(function(name){
       $("#names").append("<option>"+name+"</option>");
      });
      $("#names").val(localStorage.callname);
      filter();
    }


    function update(){
      
      var url = "crews.php";
      checkDates();
      
      $("#msg").show();
      
      $.get(url, {
        start:startDate,
        end:endDate
      },function(data){
        $("#main").html("");
        d=JSON.parse(data);
        d=JSON.parse(d.data);
        //Find Engine 24 Crew
        d.forEach(function(loc){
          loc.users.forEach(function(user){
            var name = user.lastname+",<br>"+user.firstname;
            name = name.toUpperCase();
            name = name.split(" ");
            names.push(name[0]);
            //console.log(user.lastname);
            //console.log(loc);
            if(user.labels){
              if(user.labels.length < 1){
                var label = "";
              }else{
                var label = user.labels[0].label;
              }
            }

            if(loc.id != 0){
              var day = user.real_start.split(" ");
              day = day[0].split("-");

              var dateClass = day[1] + day[2] + day[0];
              var noteDay = day[0] +"-"+ day[1] +"-"+ day[2];
              day = day[1] + "/" + day[2] + "/" + day[0];
              
              //console.log(dateClass);
              var id = Math.floor(Math.random()*100000000000);
              if($("#" + dateClass).length < 1){
                $("#main").append('<div class="col-sm-12 well item days" id="'+dateClass+'"></div>');

                var noteURL="notes.php?start=" + noteDay;
                $.get(noteURL,function(data){
                  var data = JSON.parse(data);
                  data = data[0];
                  var notes = data.notes;
                  var activities = data.activities;
                  if(notes == null){notes="No Notes"};
                  if(activities == null){activities="No Activities"};
                  notes = notes.replace(/<.*?script.*?>.*?<\/.*?script.*?>/igm, '');
                  activities = activities.replace(/<.*?script.*?>.*?<\/.*?script.*?>/igm, '');
                  console.log(notes.notes); 
                  $("#" + dateClass).prepend('<div class="well notes">'+notes+'<br>'+activities+'</div>');
                  $("#" + dateClass).prepend('<h1>'+day+'</h1>');
                }).done(function(){
                  colors();
                });
              }
              
              $("#" + dateClass).append('<div class="well item '+label+'" ><h1 class="case">'+user.lastname+",<BR>"+user.firstname+'</h1>'+
                '<p class="case"><b>' + day +'</b><br>'+ 
                loc.title + ' ' + label + '<br>'+
                //user.real_start + " to " + user.real_end + "<br>"+
                user.work_type + "</p>"+
                '</div>');
            }else{
              $("3" + dateClass).append('<div class="well item"><h1 class="case">'+user.firstname+",<BR>"+user.lastname+'</h1>'+
              '</div>');
            }
          });
            
        });
      }).done(function(){
        $("#msg").hide();
        $(".case").each(function(){ 
          var text = $(this).html().toUpperCase();
          $(this).html(text);
        });
        nameList(); 
        colors();
        sortDays();
      });

    }

    function defaultDates(){
      var start = new Date();
      var end = new Date();
      end.setDate(start.getDate() + 12);

      var day = ("0" + start.getDate()).slice(-2);
      var month = ("0" + (start.getMonth() + 1)).slice(-2);
      var today = start.getFullYear()+"-"+(month)+"-"+(day) ;
      $("#start").val(today);

      day = ("0" + end.getDate()).slice(-2);
      month = ("0" + (end.getMonth() + 1)).slice(-2);
      var endDay = end.getFullYear()+"-"+(month)+"-"+(day) ;
      $("#end").val(endDay);

    }

    function checkDates(){
      startDate = new Date($("#start").val()).getTime()/1000;
      endDate = new Date($("#end").val()).getTime()/1000;
      //console.log(startDate + " : " + endDate);
      if(startDate > endDate ){
          endDate = startDate + 86400;
          console.log(startDate + " : " + endDate);
      }
      
    }

    function sortDays(){
      //clear array
      days.length = 0;

      //add to and sort array
      $('.days').each(function() {
          days.push( this.id );
          days.sort();
      });

      days.forEach(function(id){
        var item = $("#"+id);
        $("#main").append(item);
      });
    }

  </script>
</head>
<body>

<div class="container" id="mainContainer">
  <form role="form">
    <div class="form-group">
      <label for="names">Who:</label>
      <select class="form-control" id="names"></select>

      <label for="trucks">Where:</label>
      <select class="form-control" id="trucks">
        <option></option>
        <option>ENGINE 20</option>
        <option>ENGINE 21</option>
        <option>ENGINE 22</option>
        <option>ENGINE 23</option>
        <option>ENGINE 24</option>
        <option>ENGINE 70</option>
        <option>ENGINE 71</option>
        <option>ENGINE 72</option>
        <option>ENGINE 73</option>
        <option>TOWER 72</option>
        <option>WATER TENDER 70</option>
        <option>WATER TENDER 20</option>
        <option>MEDIC 21</option>
        <option>MEDIC 70</option>
        <option>RESCUE 75</option>
      </select>

      <div class="row">
        <div class="col-sm-6">
          <label for="start">Start:</label>
          <input type="date" id="start" class="form-control date">
        </div>
        <div class="col-sm-6">
          <label for="end">End:</label>
          <input type="date" id="end" class="form-control date">
        </div>
      </div>
    </div>
  </form>
  <div class="well" id="msg"><h1>Loading...</h1></div>
  <div class="row" id="main">
  </div>
</div>

</body>
</html>

