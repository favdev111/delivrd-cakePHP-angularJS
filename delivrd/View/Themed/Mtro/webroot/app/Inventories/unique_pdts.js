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

app.controller( 'uniquePdts', ['$scope', '$http', '$modal', '$sce', function ($scope, $http, $modal, $sce) {

    $scope.currentPage = 1;
    $scope.limit = limit;

    $scope.sortBy = 'Product.name';
    $scope.sortDir = 'ASC';

    $scope.alerts = [];
    getData();

    $scope.orderBy = function(field) {
        if(field == $scope.sortBy) {
            if($scope.sortDir == 'DESC') {
                $scope.sortDir = 'ASC';
            } else {
                $scope.sortDir = 'DESC';
            }
        } else {
            $scope.sortDir = 'ASC';
        }
        $scope.sortBy = field;
        
        getData();
    }
    
    $scope.orderDisplay = function(fileld) {
        var dir_class = 'text-muted';
        var direction = 'fa fa-sort';
        if(field == $scope.sortBy) {
            dir_class = 'text-info';
            if($scope.sortDir == 'ASC') {
                direction = 'fa fa-sort-amount-asc';
            } else {
                direction = 'fa fa-sort-amount-desc';
            }
        }
        return direction+' '+dir_class;
    }

    function getData() {
        var url = siteUrl + 'inventories/ajax_unique_pdts';
        url = url +'?page='+$scope.currentPage;
        url = url +'&limit='+$scope.limit;
        
        if($scope.sortBy != '') {
            url = url +'&sortby='+$scope.sortBy;
        }

        if($scope.sortDir != '') {
            url = url +'&sortdir='+$scope.sortDir;
        }

        $http.get(url)
        .then(function(response) {
            $scope.totalItems = response.data.recordsTotal;
            angular.copy(response.data.rows, $scope.alerts);
        }, function(response) {
            if(response.status == 403) {
                window.location.href = siteUrl+'login';
            }
        });
    }

    $scope.pageChanged = function(page) {
        getData();
    };
    
    $scope.applySearch = function() {
        $scope.currentPage = 1;
        getData();
        return false;
    }

    $scope.inventoryStatus = function(line) {
        /*if( (line.Inventory.total < line.Product.reorder_point) && (line.Inventory.total > line.Product.safety_stock) ) {
            var btnclass = 'btn yellow-crusta';
        } else if(line.Inventory.total < line.Product.safety_stock ) {
            var btnclass = 'btn btn-danger'; 
        } else {
            var btnclass = 'btn btn-success';
        }*/

        if(line.Product.safety_stock == null) {
            line.Product.safety_stock = 0;
        }
        line.Inventory.total = parseInt(line.Inventory.total);
        line.Product.safety_stock = parseInt(line.Product.safety_stock);
        line.Product.reorder_point = parseInt(line.Product.reorder_point);

        if( (line.Inventory.total < line.Product.reorder_point) && (line.Inventory.total > line.Product.safety_stock || line.Product.safety_stock == 0) ) {
            var btnclass = 'btn yellow-crusta';
        } else if(line.Inventory.total < line.Product.safety_stock ) {
            var btnclass = 'btn btn-danger'; 
        } else {
            var btnclass = 'btn btn-success';
            console.log(line.Inventory.total); 5
            console.log(line.Product.reorder_point); 15
            console.log(line.Product.safety_stock); 40
        }

        return btnclass;
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