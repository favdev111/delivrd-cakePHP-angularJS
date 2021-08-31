var app = angular.module('delivrd-app',['ui.bootstrap', 'ui.bootstrap.modal', 'template/modal/backdrop.html', 'template/modal/window.html']);
    
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

app.controller( 'FieldsList', ['$scope', '$http', '$modal', '$sce', function ($scope, $http, $modal, $sce) {

    $scope.showUsage = function(field_id) {
        var modalUsageInstance = $modal.open({
            templateUrl: siteUrl+'fields/usage/'+field_id+'?bust='+Math.random().toString(36).slice(2),
            controller: 'modalUsageInstanceCtrl',
            resolve: {
                field_id: function () {
                    return field_id;
                }
            }
        });

        modalUsageInstance.result.then(function (data) {
            
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