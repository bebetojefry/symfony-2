angular.module('modalForm', []).config(function($interpolateProvider){
    $interpolateProvider.startSymbol('[[').endSymbol(']]');
}).controller('AppController', function($scope, $http) {
    $scope.modalHTML = '';
    $scope.modalUrl = '';
    $scope.modal = null;
    
    $scope.newModal = function(modalUrl){
        $scope.modalUrl = modalUrl;
        $scope.setup();
    }
 
    // generate modal html
    $scope.setup = function(){
        $http.get($scope.modalUrl)
        .success(function (response) {
            $scope.modalHTML = response;
            $scope.createModalEvents();
        });
    }
        
    // set modalHTMl & event handlers
    $scope.createModalEvents = function(){
        $scope.modal = $('.modal').modal();
    }
});