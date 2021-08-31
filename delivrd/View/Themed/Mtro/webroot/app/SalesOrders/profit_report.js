var app = angular.module('delivrd-app',['ui.bootstrap']);
var change = true;

app.config(function ($httpProvider) {
    $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
});

app.directive("datepickercustom", function($timeout, $parse) {
    return {
        restrict: 'A',
        require : 'ngModel',
        link : function (scope, element, attrs, ngModelCtrl) {
            
            element.bind('change', function(e){
                //triggered event if change
                if(change) {
                    change = false;
                    scope.$apply(function () {
                        ngModelCtrl.$setViewValue(element.val());
                    });
                }
                setTimeout(function() {
                    change = true;
                }, 100);
            });

            /*element.datepicker({
                rtl: Metronic.isRTL(),
                autoclose: true,
                todayHighlight: true,
                format: 'yyyy-mm-dd'
            }).change(function (date) {
                if(change) {
                    change = false;
                    // Triggers a digest to update your model
                    console.log(date._d);
                    scope.$apply(function () {
                        ngModelCtrl.$setViewValue('2019-01-01');
                    });
                }
            });*/
        }
    }
});

app.controller( 'ProfitRport', ['$scope', '$http', '$sce', function ($scope, $http, $sce) {
    
    $scope.currentPage = 1;
    $scope.limit = limit;
    $scope.maxSize = 100;

    $scope.status_id = [];
    $scope.customer = [];
    $scope.products = [];
    $scope.start_date = '';
    $scope.end_date = '';

    $scope.sortBy = 'OrdersLine.created';
    $scope.sortDir = 'DESC';

    $scope.orderlines = [];
    $scope.totals = 0;

    $scope.showTotalsBlock = 0;
    $scope.total_profit = 0;
    $scope.profit_margin = 0;
    $scope.average_margin = 0;

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
        var url = siteUrl + 'salesorders/profit_report_js';
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

        if($scope.products != '') {
            angular.forEach($scope.products, function(value,index){
                url = url +'&products[]='+value;
            });
        }

        if($scope.start_date != '') {
            url = url +'&start_date='+$scope.start_date;
        }

        if($scope.end_date != '') {
            url = url +'&end_date='+$scope.end_date;
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

            //$scope.total_profit = response.data.total_profit;
            //$scope.profit_margin = response.data.profit_margin;
            //$scope.average_margin = response.data.average_margin;

        }, function(response) {
            if(response.status == 403) {
                window.location.href = siteUrl+'login';
            }
        });
    }

    $scope.showTotals = function() {
        $('#showTotals').html('<i class="fa fa-spinner fa-spin fa-fw"></i> loading...').addClass('disabled');
        var url = siteUrl + 'salesorders/profit_totals?1=1';
        
        if($scope.customer != '') {
            angular.forEach($scope.customer, function(value,index){
                url = url +'&customer[]='+value;
            });
        }

        if($scope.products != '') {
            angular.forEach($scope.products, function(value,index){
                url = url +'&products[]='+value;
            });
        }

        if($scope.start_date != '') {
            url = url +'&start_date='+$scope.start_date;
        }

        if($scope.end_date != '') {
            url = url +'&end_date='+$scope.end_date;
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
            $('#showTotals').html('Show Totals').removeClass('disabled');
            $scope.total_profit = response.data.total_profit;
            $scope.profit_margin = response.data.profit_margin;
            $scope.average_margin = response.data.average_margin;
            $scope.showTotalsBlock = 1;
        }, function(response) {
            if(response.status == 403) {
                window.location.href = siteUrl+'login';
            }
        });
        return false;
    }

    $scope.pageChanged = function(page) {
        getData();
    };
    
    $scope.applySearch = function() {
        $scope.showTotalsBlock = 0;

        $scope.currentPage = 1;
        getData();
        return false;
    }

    $scope.priceDisplay = function(value) {
        return parseFloat(value).toFixed(2);
    }

    $scope.marginPurchaseClass = function(line) {
        if((line.OrdersLine.unit_price - line[0].purchase_price) >= 0) {
            return 'text-success';
        } else {
            return 'text-danger';
        }
    }

    $scope.marginProductClass = function(line) {
        if((line.OrdersLine.unit_price - line.Product.product_price) >= 0) {
            return 'text-success';
        } else {
            return 'text-danger';
        }
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