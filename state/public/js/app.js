var routeApp = angular.module('routerApp',['ui.router','ngDialog']);
routeApp.config(function($stateProvider,$urlRouterProvider){
    $urlRouterProvider.otherwise('home');
    $stateProvider.state("home",{
        url:"/home",
        templateUrl:"template/home.html"
     })
        .state('list',{
            url:"/list",
            templateUrl:"template/home-list.html"
        })
        .state('about',{
            templateUrl:"template/home-about.html",
            controller:"aboutController"
        })
        .state('profilelist',{
            url:'/profilelist',
            templateUrl:"template/home-profilelist.html",
            controller:"profilelistController"
        })
        .state("profiledetails",{
            url:"/profile/:id",
            templateUrl:"template/profile.html",
            controller:"profileController"
        });
    /*$locationProvider.html5Mode(true);*/
});
routeApp.run(
    function($rootScope,$location,ngDialog,profilelists){
        $rootScope.signout = function(){
        $(".profilelist, .signout").hide();
        $(".login1, .signup, .modal-backdrop.fade.in, #login-modal").show();
            $location.path("/home");
    }
        //$rootScope.clickToOpen = function (id) {
        //    ngDialog.open({ template: 'templateId' });
        //    angular.forEach(profilelists.profilelist, function(value, key){
        //        angular.forEach(value, function(val,ky){
        //            if(val.id == id){
        //                $rootScope.viewprofile = val;
        //                //console.log(val);
        //            }
        //        });
        //    });
        //};
    });

//routeApp.run(function($scope,$location){
//    $scope.signout = function(){
//        $(".profilelist, .signout").hide();
//        $(".login1, .signup, .modal-backdrop.fade.in, #login-modal").show();
//        $location.path("/home");
//    }
//});
routeApp.controller("profileController",function($scope,$stateParams,profilelists){
    //console.log($stateParams.id);
    angular.forEach(profilelists.profilelist, function(value, key){
        angular.forEach(value, function(val,ky){
            if(val.id == $stateParams.id){
                $scope.viewprofile = val;
                //console.log(val);
            }
        });
    });

    //$scope.clickToOpen = function () {
    //    ngDialog.open({ template: 'templateId' });
    // };
    //$scope.submit = function(){
    //    console.log($scope.viewprofile.name);
    //};
});
routeApp.controller("loginController",function($scope,$filter,$location,profilelists){
    $scope.loginfnc = function(){
        console.log(profilelists.profilelist.profile);
        $scope.newTemp = $filter("filter")(profilelists.profilelist.profile, {email:$scope.email,password:$scope.pass});
        $scope.myValue=true;
        if($scope.newTemp[0]){
            $(".profilelist, .signout").show();
            $(".login1, .signup, .modal-backdrop.fade.in, #login-modal").hide();
            $location.path('/profilelist');
            console.log($scope.newTemp);
            console.log("login success");
        }else{
            $scope.errormsg="Invalid Login credential";
            console.log()
            console.log("invalid login");
        }

        console.log($scope.email);
        console.log($scope.pass);
    }
});
routeApp.controller("signupController",function($scope,profilelists){
    $scope.signup = function(){
        profilelists.profilelist.profile.push({name:$scope.name,email: $scope.email,password:$scope.pass});
        $scope.name = "";
        $scope.email = "";
        $scope.pass = "";
        $("#signup-modal,.modal-backdrop.fade.in").hide();
         console.log(profilelists.profilelist.profile);
    };
});
routeApp.controller("profilelistController",function($scope,profilelists,ngDialog){
    $scope.profilelist = profilelists.profilelist;
    $scope.clickToOpen = function (ids) {
        angular.forEach($scope.profilelist.profile, function(value, key){
            if(value.id == ids){
                $scope.viewdtl = value;
            }
         });
        ngDialog.open({ template: 'templateId',scope:$scope});


    };
    console.log($scope.profilelist);
});
routeApp.controller("aboutController",function($scope){
    $scope.about = "This is about us page";
});
routeApp.service("profilelists",function(){
    this.profilelist = {'profile':[
        {id:1,name:'Pranabesh Chand',email:'pchand@mindinmotion.co',password:'1212',description:'Progress on Angularjs'},
        {id:2,name:'Alex',email:'alex@gmail.com',password:'1212',description:'Progress on Nodejs'},
        {id:3,name:'chris williums',email:'chris@gmail.com',password:'1212',description:'Progress on cakephp'},
        {id:4,name:'Peter scott',email:'peter@gmail.com',password:'1212',description:'No Progress'},
        {id:5,name:'sameer kar',email:'sameer@gamil.com',password:'1212',description:'Progress on mongo'}
    ]};
});