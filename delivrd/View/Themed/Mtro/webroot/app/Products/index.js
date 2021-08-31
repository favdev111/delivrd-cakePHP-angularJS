var app = angular.module('delivrd-app',['ui.bootstrap', 'ui.bootstrap.modal', 'template/modal/backdrop.html', 'template/modal/window.html']);
    
app.directive('fileModel', ['$parse', function ($parse) {
    return {
       restrict: 'A',
       link: function(scope, element, attrs) {
          var model = $parse(attrs.fileModel);
          var modelSetter = model.assign;
          
          element.bind('change', function() {
             scope.$apply(function() {
                modelSetter(scope, element[0].files[0]);
             });
          });
       }
    };
 }]);

app.service('fileUpload', ['$http', function ($http) {
    this.uploadFileToUrl = function(file, uploadUrl){
        var fd = new FormData();
        fd.append('file', file);
        $http.post(uploadUrl, fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .success(function(){
        })
        .error(function(){
        });
    }
}]);

angular.module("template/modal/backdrop.html", []).run(["$templateCache", function($templateCache) {
  $templateCache.put("template/modal/backdrop.html",
    "<div class=\"modal-backdrop fade\" ng-class=\"{in: animate}\" ng-style=\"{'z-index': 10040 + index*10}\" ng-click=\"close($event)\"></div>");
}]);

angular.module("template/modal/window.html", []).run(["$templateCache", function($templateCache) {
    $templateCache.put("template/modal/window.html",
    "<div class=\"modal fade {{ windowClass }}\" ng-class=\"{in: animate}\" style='display:block;z-index:10050'  ng-transclude></div>");
}]);

app.config(function ($httpProvider) {
    $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
});

app.controller( 'ProductList', ['$scope', '$http', '$modal', '$sce', function ($scope, $http, $modal, $sce) {

    $scope.addDocument = function(model_id, product_sku) {
        var modalDocumentInstance = $modal.open({
            templateUrl: siteUrl+'documents/view/product/'+model_id+'?f=l&bust='+Math.random().toString(36).slice(2),
            controller: 'modalDocumentInstanceCtrl',
            resolve: {
                model_id: function () {
                    return model_id;
                },
                model_type: function () {
                    return 'product';
                },
                model_name: function () {
                    return product_sku;
                }
            }
        });

        modalDocumentInstance.result.then(function (data) {
            
        });
    }
}]);

app.factory('HttpInterceptor', function($templateCache, APP_CONFIG) {
    return {
        'request': function(request) {
            if (request.method === 'GET' && $templateCache.get(request.url) === undefined) {
                request.url += '?ver=' + APP_CONFIG.VERSION;
            }
            return request;
        }
    };
});