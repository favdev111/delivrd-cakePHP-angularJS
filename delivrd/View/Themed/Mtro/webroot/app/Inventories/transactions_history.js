var app = angular.module('delivrd-app',['ui.bootstrap', 'ui.bootstrap.modal', "template/modal/backdrop.html","template/modal/window.html"]);

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

app.controller( 'transactionController', ['$scope', '$http', '$modal', '$sce', function ($scope, $http, $modal, $sce) {
    $scope.product = product;
    $scope.is_cum_qty = 1;
    $scope.sortType = ['date'];

    $scope.selectedItem = '';
    $scope.query = '';

    $scope.maxSize = 30;
    $scope.currentPage = 1;
    $scope.limit= limit;//10;
    $scope.default_limit = limit;

    $scope.transactions = [];
    getData();


    function getData() {
        var url = siteUrl + 'inventories/tx_history/'+$scope.product.Product.id;
        url = url +'/page:'+$scope.currentPage;
        url = url +'/limit:'+$scope.limit;

        if($scope.selectedItem != '') {
            url = url +'/location:'+$scope.selectedItem;
        }
        if($scope.query != '') {
            url = url +'/q:'+$scope.query;
            $scope.is_cum_qty = 0;
        } else {
            $scope.is_cum_qty = 1;
        }
        $http.get(url)
            .then(function(response) {
                $scope.totalItems = response.data.recordsTotal
                angular.copy(response.data.rows, $scope.transactions)
                acharts.initCharts(response.data.chartd1, response.data.chartd2, response.data.chartd3);
            }, function(response) {
                if(response.status == 403) {
                    window.location.href = siteUrl+'login';
                }
            });
    }

    $scope.pageChanged = function(page) {
        getData();
    };

    /*$scope.$watch('search', function (newVal, oldVal) {

    });*/

    $scope.showAll = function() {
        $scope.selectedItem = '';
        $scope.query = '';
        $scope.currentPage = 1;
        $scope.limit = $scope.limit= limit;
        $('select.limit').trigger('change');
        getData();
        return false;
    };

    $scope.applySearch = function() {
        $scope.currentPage = 1;
        $('select.limit').trigger('change');
        getData();
    };

    $scope.product_name = function($product) {
        var substr = $product;
        if($product) {
            if($product.length > 28) {
                substr = $product.substring(0, 25) + '...';
            }
        } else {
            substr = '';
        }
        
        var $text = '<span data-toggle="tooltip" data-placement="bottom" title="'+$product+'">'+substr+'</span>';
        return $sce.trustAsHtml($text);
    }


    $scope.alignQty = function(product_id, warehouse_id) {
        
        var ModalAlignQtyInstance = $modal.open({
            templateUrl: siteUrl+'inventories/align_qty/'+product_id+'/'+warehouse_id+'?bust='+Math.random().toString(36).slice(2),
            controller: ModalAlignQtyInstanceCtrl
        });

        ModalAlignQtyInstance.result.then(function (data) {
            if(data.action == 'success') {
                
                // change issue values
                /*$.each($scope.lines, function(key, val) {
                    $scope.lines[key].OrdersLine.sentqty = val.OrdersLine.quantity;
                });*/
                toastr["success"](data.msg);
            } else {
                toastr[data.action](data.msg);
            }
        });
            
    }

    var ModalAlignQtyInstanceCtrl = function($scope, $modalInstance, $http, $rootScope) {
        $scope.inventoryAlign = function(product_id, warehouse_id) {
            
            var url = siteUrl+'inventories/align/'+product_id+'/'+warehouse_id;

            $http({
                method  : 'POST',
                url     : url,
                data    : 'order_id='+order_id,
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data) {
                if (data.action == 'success') {
                    $modalInstance.close(data);
                } else {
                    $modalInstance.close(data);
                }
            })
        };

        $scope.close = function (e) {
            e.preventDefault();
            $modalInstance.dismiss('cancel');
        };
    }
}]);