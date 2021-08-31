app.controller( 'modalUsageInstanceCtrl', ['$scope', '$modalInstance', '$http', 'field_id', function ($scope, $modalInstance, $http, field_id) {


    $scope.sortBy = 'Product.modified';
    $scope.sortDir = 'DESC';

    $scope.totalItems = 0;
    $scope.products = [];
    $scope.query = '';
    $scope.currentPage = 1;

    getProducts();

    function getProducts() {
        var url = siteUrl+'fields/usage_ajax/'+field_id+'?bust='+Math.random().toString(36).slice(2);
        url = url +'&page'+$scope.currentPage;
        
        if($scope.query != '') {
            url = url +'&q='+$scope.query;
        }
        console.log($scope.query)

        $http.get(url).then(function(response) {
            $scope.totalItems = response.data.recordsTotal;
            angular.copy(response.data.rows, $scope.products);
        });
    }

    $scope.pageChanged = function(page) {
        getProducts();
    };

    $scope.applySearch = function() {
        var $ctrl = this;
        $scope.query = $ctrl.query;
        
        $scope.currentPage = 1;
        getProducts();
    };
    
    $scope.close = function (e) {
        e.preventDefault();
        $modalInstance.dismiss('cancel');
    };
}]);