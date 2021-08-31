var app = angular.module('delivrd-app',['ui.bootstrap']);

app.config(function ($httpProvider) {
    $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
});

app.controller( 'userList', ['$scope', '$http', '$modal', '$sce', function ($scope, $http, $modal, $sce) {
    
    $scope.currentPage = 1;
    $scope.limit = limit;
    $scope.maxSize = 30;

    $scope.sortBy = 'User.created';
    $scope.sortDir = 'DESC';

    $scope.users = [];
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
        var url = siteUrl + 'admin/users/index_js';
        url = url +'/page:'+$scope.currentPage;
        url = url +'?limit='+$scope.limit;
        
        if($scope.sortBy != '') {
            url = url +'&sortby='+$scope.sortBy;
        }

        if($scope.sortDir != '') {
            url = url +'&sortdir='+$scope.sortDir;
        }
        
        $http.get(url)
        .then(function(response) {
            $scope.totalItems = response.data.recordsTotal;
            angular.copy(response.data.rows, $scope.users);
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
        //$('select.limit').trigger('change');
        getData();
        return false;
    }

    $scope.showAll = function() {
        $scope.currentPage = 1;
        $scope.limit = limit;

        $scope.sortBy = 'User.created';
        $scope.sortDir = 'DESC';
        //$('#OrderStatusId').trigger('change');
        getData();
        return false;
    }
}]);