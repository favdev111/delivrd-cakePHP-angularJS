var app = angular.module('delivrd-app',['ui.bootstrap', 'template/modal/backdrop.html', 'template/modal/window.html']);

angular.module("template/modal/backdrop.html", []).run(["$templateCache", function($templateCache) {
  $templateCache.put("template/modal/backdrop.html",
    "<div class=\"modal-backdrop fade\" ng-class=\"{in: animate}\" ng-style=\"{'z-index': 10040 + index*10}\" ng-click=\"close($event)\"></div>");
}]);

angular.module("template/modal/window.html", []).run(["$templateCache", function($templateCache) {
    $templateCache.put("template/modal/window.html",
    "<div class=\"modal fade {{ windowClass }}\" ng-class=\"{in: animate}\" style='display:block;z-index:10050'  ng-transclude></div>");
}]);

app.controller( 'AddField', ['$scope', '$http', '$modal', function ($scope, $http, $modal) {
    $scope.field_options = field_options;

    $scope.removeOption = function($id) {
        
        angular.forEach($scope.field_options, function(value, index){
            if(value.FieldsValue.id == $id) {
                $scope.field_options.splice(index,1);
                $scope.field_options.splice(index,0);
            }
        });
        toastr["success"]('Option successfully deleted.');
            
    }

    $scope.addOption = function() {

        if($scope.new_option == undefined) {
            toastr.error('Please enter valid Option.');
            return false;
        }
        var pr = { 
            'FieldsValue': {
                'id': Math.random().toString(36).slice(2),
                'value': $scope.new_option
            }
        };
        $scope.field_options.push(pr);
        $scope.new_option = '';
        toastr["success"]('Option successfully added.');
            
    }

}]);