/*主控制器*/
app.controller('mainCtrl',['$scope','$rootScope','$location','$timeout','$http','$cookies','$cookieStore',function($scope,$rootScope,$location,$timeout,$http,$cookies,$cookieStore){
    console.log('主控制器');
}])
/*直播列表*/
.controller('liveList',['$scope','$rootScope','$location','$timeout','$http','$cookies','$cookieStore',function($scope,$rootScope,$location,$timeout,$http,$cookies,$cookieStore){
    console.log('直播列表');

	/*直播列表*/
    // $http.post(
    //     url+"App/Index/live_list", $.param({
    //         uid : ,
    //         page: 
    //     }),
    //     {headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}}
    // ).success(function (data) {
    //     console.log(data)
    //     if (data['status'] == 'ok') {
    //         $scope.goodsInfo = data['info'];
    //     } else if (data['status'] == 'error') {
    //         console.log(data['status']);
    //     } else if (data['info'] == 'token failed') {
    //     }
    // });
}])
/*直播间*/
.controller('liveRoom',['$scope','$rootScope','$location','$timeout','$http','$cookies','$cookieStore',function($scope,$rootScope,$location,$timeout,$http,$cookies,$cookieStore){
    console.log('直播间');
}])