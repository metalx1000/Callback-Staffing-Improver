var s = document.createElement('script');
// TODO: add "script.js" to web_accessible_resources in manifest.json
s.src = chrome.extension.getURL('html_code.js');
s.onload = function() {
      this.parentNode.removeChild(this);
};

var reps = [
  "Sick Leave|Slacker",
  "Kelly Day|Money Ain't Important to Me",
  "Shift Exchange|<a href='#' class='behere'>I'm not even supposed to be here today</a>",
  "Vacation|<a href='#' class='vac'>At Wally World</a>",
  "Personal Leave|Got Something To Do",
  "Open slot|Should there be someone here?",
  "Grant Danskine|R2D2",
  "Kristofer Occhipinti|Frank Castle", 
  "Charles Melheim|CJ Melheim"
]; 

(document.head||document.documentElement).appendChild(s);
$(document).ready(function(){
  console.log("Script Running");
  var name = "Frank Castle"
  var icon = "http://cdn-img.easyicon.net/png/10669/1066998.gif";  
  var icon2 = "http://imgbin.org/images/24149.gif"

  //Change main logo and title
  $(".logo-wrapper").find('img').attr('src',icon);
  $(".logo-wrapper").find('span').html(name);

  //Change user drop down menu
  $(".userinfo").find('strong').html(name);
  $(".userinfo").find('img')[0].src = icon2;

  //replaces APP links in footer with music player
  //Who in their right mind would install an APP when there is a web UI?
  var music = [
    "http://s.myfreemp3.biz/p.php?q=15286917_83105639_654c90ed9d_/",
    "http://s.myfreemp3.biz/p.php?q=-3492918_42226624_654c90ed9d_/",
    "http://s.myfreemp3.biz/p.php?q=18349462_129198673_654c90ed9d_/",
    "http://s.myfreemp3.biz/p.php?q=15286917_83105639_654c90ed9d_/"
  ];
  var song = music[Math.floor(Math.random() * music.length)];

  var audio = $('<audio />', {
    id : 'player',
    autoPlay : 'autoplay',
    controls : 'controls',
    src : song
  });
  $(".footer").find('p').html(audio);
  $("#player")[0].volume=.5;

  //updater
  update();
  setTimeout(function(){
    update();
  },2000);

  
  $("body").click(function(){
    setTimeout(function(){
      update();
    },2000);  
  });
}); 

function update(){
  $(".set-day-notes").addClass("active"); 

  //Change notes/activity title
  $(".notes-title").html("War Journal");

  //replace names
  for(var i=0;i<reps.length;i++){
      var text = reps[i].split("|");
      $(".fc-event-user").each(function(){
        var name = $(this).html().replace(text[0],text[1]);
        $(this).html(name);
      });
  };

  $(".vac").click(function(){
    $("#player").attr('src', 'http://www.moviesounds.com/vacation/vacatin9.wav');
  });

  $(".behere").click(function(){
    $("#player").attr('src', 'http://www.moviewavs.com/0053148414/MP3S/Movies/Clerks/heretoday.mp3');
  });
  console.log("updated!!!");

}
