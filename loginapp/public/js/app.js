var app = angular.module("myapp",['ui.router','ngDialog']);
app.config(function($stateProvider,$urlRouterProvider){
    $urlRouterProvider.otherwise('home');
    $stateProvider.state("home",{
        url:"/home",
        templateUrl:"template/home.html"
    })
    .state('profile',{
        url:'/profile',
        templateUrl:"template/profile.html"
    });
});

app.run(function($rootScope,ngDialog){
   $rootScope.login = function(){
       ngDialog.open({template:'loginid'});
   } ;
    $rootScope.signup = function(){
        ngDialog.open({template:'signup',scope:$rootScope});
    };

});
app.controller("loginController",function($scope){
    $scope.loginsubmit = function(){
        alert("hello");
    };
});

app.controller("signupController",function($scope){
    $scope.signupsubmit = function(){
        alert("hello signup");
    };
});