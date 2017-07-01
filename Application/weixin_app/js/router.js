var url = "http://mse.tstmobile.com/";

var app=angular.module("app",['ng','ngRoute','ngCookies',"ngTouch","ngAnimate"]);

/*路由*/
app.config(function($routeProvider,$locationProvider){
    $routeProvider
	/*直播列表*/
    .when("/",{
        templateUrl:"views/live/liveList.html",
        controller:"liveList"
    })
	/*直播列表*/
    .when("/liveList",{
        templateUrl:"views/live/liveList.html",
        controller:"liveList"
    })    
    /*直播间*/
    .when("/liveRoom",{
        templateUrl:"views/live/liveRoom.html",
        controller:"liveRoom"
    })
    .otherwise({
        redirectTo: "/"
    })
})