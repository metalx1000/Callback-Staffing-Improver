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
      
      var url = "https://www.callbackstaffing.com/Application/Ajax/CrewScheduler/";
      checkDates();
      
      $("#msg").show();
      
      $.get(url, {
        start:startDate,
        end:endDate,
        dataChanged:0
      },function(data){
        $("#main").html("");
        d=JSON.parse(data.data);
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

                var noteURL="https://www.callbackstaffing.com/Application/Ajax/DayNotes/GetByDate/?start=" + noteDay;
                $.get(noteURL,function(data){
                  var data = data[0];
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

