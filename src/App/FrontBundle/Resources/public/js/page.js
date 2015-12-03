var modal = angular.module('modal', []);

// config override to avoid conflict with twig syntax
modal.config(function($interpolateProvider){
    $interpolateProvider.startSymbol('[[').endSymbol(']]');
});

// html filter with SCE(Script Contextual Escaping)
modal.filter('unsafe', function($sce) {
    return function(val) {
        return $sce.trustAsHtml(val);
    };
});

// Main Controller for modal
modal.controller('MainCtrl', function ($scope, $http) {
    $scope.showModal = false;
    $scope.toggleModal = function(obj){
        $scope.modalTitle = obj.target.attributes.modalTitle.value;
        $http.get(obj.target.attributes.modalUrl.value)
        .success(function (response) {
            $scope.processResponse(response);
            $scope.showModal = !$scope.showModal;
        })
        .error(function(response){
            $scope.errorhandler(response);
        });
        
        $scope.formAction = obj.target.attributes.formAction.value;
        $('#modal-form').ajaxForm({
            success: function(response) {
                $scope.processResponse(response);
            },
            error: function(response){
                $scope.errorhandler(response);
            }
        });
    };
    
    $scope.confirmExecuteUrl = function(obj){
        BootstrapDialog.show({
            title: Translator.trans('globals.confirm'),
            message: obj.target.attributes.cofirmText.value,
            buttons: [{
                label: Translator.trans('globals.yes'),
                cssClass: 'btn-primary',
                action: function(dialog) {
                    dialog.close();
                    $http.get(obj.target.attributes.targetUrl.value)
                    .success(function (response) {
                        $scope.processResponse(response);
                    })
                    .error(function(response){
                        $scope.errorhandler(response);
                    });
                }
                }, {
                label: Translator.trans('globals.no'),
                cssClass: '',
                action: function(dialog) {
                    dialog.close();
                }
            }]
        });
    };
    
    $scope.errorhandler = function(response){
        if(response.status == 304){
            alert(Translator.trans('globals.session.expired'));
        } else {
            alert(Translator.trans('globals.error.something'));
        }
        location.reload(true);
    }
    
    $scope.processResponse = function(response){
        try { response = jQuery.parseJSON(response); } catch(err) {}
        switch(response.code){
            case 'FORM':
                $scope.modalHtml = response.data;
                break;
            case 'FORM_REFRESH':
                $scope.$apply(function() {
                    $scope.modalHtml = response.data;
                });
                break;
            case 'REFRESH':
                location.reload(true);
                break;
            case 'REDIRECT':
                window.location = response.url;
                break;
        }
    };
});

modal.directive('modal', function () {
    return {
      template: '<div class="modal fade">' + 
          '<div class="modal-dialog">' + 
            '<div class="modal-content">' + 
              '<div class="modal-header">' + 
                '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' + 
                '<h4 class="modal-title">[[ title ]]</h4>' + 
              '</div>' + 
              '<div class="modal-body" ng-transclude></div>' + 
            '</div>' + 
          '</div>' + 
        '</div>',
      restrict: 'E',
      transclude: true,
      replace:true,
      scope:true,
      link: function postLink(scope, element, attrs) {
            scope.$watch(attrs.visible, function(value){
                scope.title = attrs.title;
                if(value == true)
                    $(element).modal('show');
                else
                    $(element).modal('hide');
                
                $('#modal-form').validator();
            });

            $(element).on('shown.bs.modal', function(){
                scope.$apply(function(){
                    scope.$parent[attrs.visible] = true;
                });
            });

            $(element).on('hidden.bs.modal', function(){
                scope.$apply(function(){
                      scope.$parent[attrs.visible] = false;
                });
            });
        }
    };
});