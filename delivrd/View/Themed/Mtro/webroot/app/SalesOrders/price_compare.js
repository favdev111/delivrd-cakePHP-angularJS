var app = angular.module('delivrd-app',['ui.bootstrap']);

app.config(function ($httpProvider) {
    $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
});

app.controller( 'prReport', ['$scope', '$http', '$modal', '$sce', function ($scope, $http, $modal, $sce) {
    
    $scope.currentPage = 1;
    $scope.limit = limit;
    $scope.maxSize = 100;

    $scope.status_id = '';
    $scope.schannel_id = '';
    $scope.difference = '';
    $scope.product_id = '';

    $scope.sortBy = 'OrdersLine.modified';
    $scope.sortDir = 'DESC';

    $scope.orderlines = [];
    $scope.totals = 0;
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
        var url = siteUrl + 'salesorders/price_compare_js';
        url = url +'/page:'+$scope.currentPage;
        url = url +'?limit='+$scope.limit;
        
        if($scope.sortBy != '') {
            url = url +'&sortby='+$scope.sortBy;
        }

        if($scope.sortDir != '') {
            url = url +'&sortdir='+$scope.sortDir;
        }

        if($scope.schannel_id != '') {
            url = url +'&schannel_id='+$scope.schannel_id;
        }
        if($scope.status_id != '') {
            url = url +'&status_id='+$scope.status_id;
        }
        if($scope.product_id != '') {
            url = url +'&product_id='+$scope.product_id;
        }
        if($scope.difference != '') {
            url = url +'&difference='+$scope.difference;
        }
        
        $http.get(url)
        .then(function(response) {
            $scope.totalItems = response.data.recordsTotal;
            angular.copy(response.data.rows, $scope.orderlines);
            $scope.totals = response.data.recordsTotal;
        }, function(response) {
            if(response.status == 403) {
                window.location.href = siteUrl+'login';
            }
        });
    }

    $scope.pageChanged = function(page) {
        getData();
    };
    
    var self = this;

    $scope.applySearch = function() {
        $scope.currentPage = 1;
        getData();
        return false;
    }

    $scope.clearProduct = function() {
        $scope.currentPage = 1;
        $scope.limit = limit;

        $scope.product_id = '';
        $('#product_auto').val('');
        
        getData();
        return false;
    }

    $scope.showAll = function() {
        $scope.currentPage = 1;
        $scope.limit = limit;

        $scope.sortBy = 'OrdersLine.modified';
        $scope.sortDir = 'ASC';

        $scope.status_id = '';
        $scope.schannel_id = '';
        $scope.difference = '';
        $scope.product_id = '';
        $('#product_auto').val('');
        
        getData();
        return false;
    }

    $scope.priceDefDisplay = function(row) {
        var $price_text = row.Product.value;
        if(row.Product.value != '' && parseFloat(row.Product.value) != parseFloat(row.OrdersLine.unit_price)) {
            $price_text = '<span class="text-danger">'+parseFloat($price_text).toFixed(2)+'</span>';
        } else if(row.Product.value != '') {
            $price_text = parseFloat($price_text).toFixed(2);
        }

        return $sce.trustAsHtml($price_text);
    }
    $scope.priceChannelDisplay = function(row) {
        var $price_text = row.ProductsPrices.value;
        if(row.ProductsPrices.value != '' && row.ProductsPrices.value != null && parseFloat(row.ProductsPrices.value) != parseFloat(row.OrdersLine.unit_price)) {
            $price_text = '<span class="text-danger">'+parseFloat($price_text).toFixed(2)+'</span>';
        } else if(row.ProductsPrices.value != '' && row.ProductsPrices.value != null) {
            $price_text = parseFloat($price_text).toFixed(2);
        }
        return $sce.trustAsHtml($price_text);
    }

    $scope.priceDisplay = function(value) {
        return parseFloat(value).toFixed(2);
    }
}]);