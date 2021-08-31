var app = angular.module('delivrd-app',['ui.bootstrap']);

app.config(function ($httpProvider) {
    $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
});

app.controller( 'CustomerRport', ['$scope', '$http', '$sce', function ($scope, $http, $sce) {
    
    $scope.currentPage = 1;
    $scope.limit = limit;
    $scope.maxSize = 100;

    $scope.status_id = [];
    $scope.customer = [];
    $scope.sortBy = 'OrdersLine.created';
    $scope.sortDir = 'DESC';

    $scope.orderlines = [];
    $scope.totals = 0;

    $scope.total_amount = 0;
    $scope.monthly_average = 0;
    $scope.count_of_month = 0;
    $scope.total_month_amount = 0;
    $scope.total_year_amount = 0;

    //$scope.totalItems = 0;

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
        var url = siteUrl + 'salesorders/customer_report_js';
        url = url +'/page:'+$scope.currentPage;
        url = url +'?limit='+$scope.limit;
        
        if($scope.sortBy != '') {
            url = url +'&sortby='+$scope.sortBy;
        }

        if($scope.sortDir != '') {
            url = url +'&sortdir='+$scope.sortDir;
        }

        if($scope.customer != '') {
            angular.forEach($scope.customer, function(value,index){
                url = url +'&customer[]='+value;
            });
        }


        if($scope.status_id != '') {
            if($scope.status_id == 50) {
                url = url +'&status_id='+$scope.status_id;
            } else {
                angular.forEach($scope.status_id, function(value,index){
                    url = url +'&status_id[]='+value;
                });
            }
        }
        
        $http.get(url)
        .then(function(response) {
            $scope.totalItems = response.data.recordsTotal;
            angular.copy(response.data.rows, $scope.orderlines);
            $scope.totals = response.data.recordsTotal;

            $scope.total_amount = response.data.total_amount;
            $scope.monthly_average = response.data.monthly_average;
            $scope.count_of_month = response.data.count_of_month;
            $scope.total_month_amount = response.data.total_month_amount;
            $scope.total_year_amount = response.data.total_year_amount;

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


    $scope.priceDisplay = function(value) {
        return parseFloat(value).toFixed(2);
    }

    $scope.status = function($status_id) {
        var $status_text;
        if($status_id == '14') {
            $status_text = "<span class='label label-default'>Draft</span>";
        } else if($status_id == '2') {
            $status_text = "<span class='label label-info'>Released</span>";
        } else if($status_id == '3') {
            $status_text = "<span class='label bg-yellow'>Shipping Processing</span>";
        } else if($status_id == '8') {
            $status_text = "<span class='label label-success'>Shipped</span>";
        } else if($status_id == '4') {
            $status_text = "<span class='label label-success'>Completed</span>";
        } else if($status_id == '50') {
            $status_text = "<span class='label label-default'>Canceled</span>";
        } else {
            $status_text = "<span class='label label-warning'>Shipped</span>";
        }
        return $sce.trustAsHtml($status_text);
    }
}]);